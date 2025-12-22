(() => {
  const cfg = window.SBPR_HOME || {};
  const canvas = document.getElementById('sbpr-canvas');
  const mosaic = document.getElementById('sbpr-mosaic');
  const magnifier = document.getElementById('sbpr-magnifier');
  const magnifierImg = document.getElementById('sbpr-magnifier-img');
  const magnifierLabel = document.getElementById('sbpr-magnifier-label');

  if (!canvas || !mosaic) return;
  const ctx = canvas.getContext('2d');

  const TILE = 24; // small tile size; the grid “reads” like a 10k wall
  const ALPHA_DEFAULT = 0.7;

  function hexPad(n, bytes){
    let hex = BigInt(n).toString(16);
    while (hex.length < bytes*2) hex = '0' + hex;
    return hex;
  }

  async function rpcCall(method, params){
    const urls = Array.isArray(cfg.rpc_urls) ? cfg.rpc_urls : [];
    let lastErr = null;
    for (const url of urls){
      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ jsonrpc: '2.0', id: 1, method, params })
        });
        const json = await res.json();
        if (json && json.result !== undefined) return json.result;
        lastErr = json && json.error ? (json.error.message || JSON.stringify(json.error)) : 'Unknown RPC error';
      } catch (e){
        lastErr = e && e.message ? e.message : String(e);
      }
    }
    throw new Error(lastErr || 'RPC failed');
  }

  async function ethCall(to, data){
    return await rpcCall('eth_call', [{ to, data }, 'latest']);
  }

  function decodeAbiString(resultHex){
    if (!resultHex || typeof resultHex !== 'string' || !resultHex.startsWith('0x')) return '';
    const hex = resultHex.slice(2);
    if (hex.length < 128) return '';
    const offset = parseInt(hex.slice(0, 64), 16) * 2;
    const len = parseInt(hex.slice(offset, offset + 64), 16) * 2;
    const start = offset + 64;
    const strHex = hex.slice(start, start + len);
    let out = '';
    for (let i = 0; i < strHex.length; i += 2){
      const code = parseInt(strHex.slice(i, i + 2), 16);
      if (!Number.isFinite(code)) break;
      out += String.fromCharCode(code);
    }
    return out;
  }

  async function fetchSvgFromChain(punkId){
    // punkImageSvg(uint16) selector = 0x74beb047
    const selector = '74beb047';
    const data = '0x' + selector + hexPad(punkId, 32);
    const res = await ethCall(cfg.data_contract, data);
    return decodeAbiString(res);
  }

  async function fetchSvg(punkId){
    const lsKey = 'sbpr_svg_' + punkId;
    try {
      const cached = localStorage.getItem(lsKey);
      if (cached && cached.startsWith('<svg')) return cached;
    } catch (e) {}

    // 1) Try WP cache endpoint (fast if already imported)
    if (cfg.svg_endpoint){
      try {
        const res = await fetch(cfg.svg_endpoint + punkId, { cache: 'force-cache' });
        if (res.ok) {
          const svg = await res.text();
          if (svg && svg.includes('<svg')) {
            try { localStorage.setItem(lsKey, svg); } catch (e) {}
            return svg;
          }
        }
      } catch (e) {}
    }

    // 2) Fall back to client-side chain call
    const svg = await fetchSvgFromChain(punkId);
    if (svg) {
      try { localStorage.setItem(lsKey, svg); } catch (e) {}
    }
    return svg;
  }

  function svgToImage(svgText){
    return new Promise((resolve, reject) => {
      const img = new Image();
      const blob = new Blob([svgText], { type: 'image/svg+xml' });
      const url = URL.createObjectURL(blob);
      img.onload = () => { URL.revokeObjectURL(url); resolve(img); };
      img.onerror = (e) => { URL.revokeObjectURL(url); reject(e); };
      img.src = url;
    });
  }

  const ids = Array.isArray(cfg.ids) ? cfg.ids.slice() : [];
  if (!ids.length) return;

  let tileMap = []; // {id, x, y, img}

  function resize(){
    const rect = mosaic.getBoundingClientRect();
    const dpr = window.devicePixelRatio || 1;
    canvas.width = Math.floor(rect.width * dpr);
    canvas.height = Math.floor(rect.height * dpr);
    canvas.style.width = rect.width + 'px';
    canvas.style.height = rect.height + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    draw();
  }

  function draw(highlightIndex = -1){
    const rect = mosaic.getBoundingClientRect();
    const w = rect.width;
    const h = rect.height;

    ctx.clearRect(0, 0, w, h);
    ctx.globalAlpha = ALPHA_DEFAULT;

    for (let i = 0; i < tileMap.length; i++){
      const t = tileMap[i];
      if (!t.img) continue;
      if (i === highlightIndex){
        ctx.globalAlpha = 1;
      } else {
        ctx.globalAlpha = ALPHA_DEFAULT;
      }
      ctx.drawImage(t.img, t.x, t.y, TILE, TILE);
    }

    ctx.globalAlpha = 1;
  }

  async function build(){
    const rect = mosaic.getBoundingClientRect();
    const cols = Math.max(1, Math.floor(rect.width / TILE));
    const rows = Math.max(1, Math.floor(rect.height / TILE));

    // Fill the wall by repeating the burned punks list.
    tileMap = [];
    let k = 0;
    for (let y = 0; y < rows; y++){
      for (let x = 0; x < cols; x++){
        const id = ids[k % ids.length];
        tileMap.push({ id, x: x * TILE, y: y * TILE, img: null });
        k++;
      }
    }

    // Load unique images once.
    const unique = [...new Set(tileMap.map(t => t.id))];
    const imageById = new Map();

    for (const id of unique){
      try {
        const svg = await fetchSvg(id);
        const img = await svgToImage(svg);
        imageById.set(id, img);
      } catch (e){
        // leave blank
      }
    }

    for (const t of tileMap){
      t.img = imageById.get(t.id) || null;
    }

    draw();
  }

  function tileAt(clientX, clientY){
    const rect = mosaic.getBoundingClientRect();
    const x = clientX - rect.left;
    const y = clientY - rect.top;
    const col = Math.floor(x / TILE);
    const row = Math.floor(y / TILE);
    const cols = Math.max(1, Math.floor(rect.width / TILE));
    const idx = row * cols + col;
    if (idx < 0 || idx >= tileMap.length) return null;
    return { idx, tile: tileMap[idx] };
  }

  function showMagnifier(tile, clientX, clientY){
    if (!magnifier || !magnifierImg) return;
    if (!tile || !tile.img) {
      magnifier.style.opacity = '0';
      return;
    }

    magnifier.style.opacity = '1';
    magnifierImg.innerHTML = '';
    const clone = tile.img.cloneNode(true);
    // Not all browsers clone Image well; fall back to <img> with same src.
    const imgEl = document.createElement('img');
    imgEl.src = tile.img.src;
    imgEl.alt = '';
    imgEl.style.width = '96px';
    imgEl.style.height = '96px';
    imgEl.style.imageRendering = 'pixelated';
    magnifierImg.appendChild(imgEl);
    if (magnifierLabel) magnifierLabel.textContent = '#' + tile.id;

    const rect = mosaic.getBoundingClientRect();
    const mx = clientX - rect.left + 16;
    const my = clientY - rect.top + 16;
    magnifier.style.transform = `translate(${mx}px, ${my}px)`;
  }

  mosaic.addEventListener('mousemove', (e) => {
    const hit = tileAt(e.clientX, e.clientY);
    if (!hit) {
      draw(-1);
      if (magnifier) magnifier.style.opacity = '0';
      return;
    }
    draw(hit.idx);
    showMagnifier(hit.tile, e.clientX, e.clientY);
  });

  mosaic.addEventListener('mouseleave', () => {
    draw(-1);
    if (magnifier) magnifier.style.opacity = '0';
  });

  mosaic.addEventListener('click', (e) => {
    const hit = tileAt(e.clientX, e.clientY);
    if (!hit) return;
    const id = hit.tile.id;
    window.location.href = '/' + id + '/';
  });

  window.addEventListener('resize', () => {
    resize();
    build();
  });

  resize();
  build();
})();
