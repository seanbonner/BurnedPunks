(() => {
  const cfg = window.SBPR_HOME || null;
  if (!cfg) return;

  const container = document.getElementById('sbpr-mosaic');
  const canvas = document.getElementById('sbpr-canvas');
  const magnifier = document.getElementById('sbpr-magnifier');
  const magImg = document.getElementById('sbpr-magnifier-img');
  const magLabel = document.getElementById('sbpr-magnifier-label');

  if (!container || !canvas) return;

  const ctx = canvas.getContext('2d', { alpha: true });
  const DPR = Math.max(1, Math.min(2, window.devicePixelRatio || 1));

  const ids = Array.isArray(cfg.ids) ? cfg.ids.slice() : [];
  const svgs = new Map();   // id -> svg text
  const bitmaps = new Map(); // id -> ImageBitmap

  let tile = 14; // px, will be recalculated
  let cols = 0, rows = 0;
  let hoverCell = -1;
  let baseBitmap = null;

  function clamp(n, a, b) { return Math.max(a, Math.min(b, n)); }

  function seededIndex(i, n) {
    // Stable-ish multiplicative hash.
    const x = (i * 2654435761) >>> 0;
    return x % n;
  }

  function setCanvasSize() {
    const rect = container.getBoundingClientRect();
    const w = Math.max(320, Math.floor(rect.width));
    const h = Math.max(360, Math.floor(rect.height));

    // target "thousands of punks" feel: small tiles.
    const target = w >= 1100 ? 12 : (w >= 800 ? 14 : 16);
    tile = target;

    cols = Math.max(10, Math.floor(w / tile));
    rows = Math.max(8, Math.floor(h / tile));

    canvas.style.width = `${cols * tile}px`;
    canvas.style.height = `${rows * tile}px`;
    canvas.width = Math.floor(cols * tile * DPR);
    canvas.height = Math.floor(rows * tile * DPR);

    ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
  }

  function fetchSvg(id) {
    if (svgs.has(id)) return Promise.resolve(svgs.get(id));
    const url = cfg.svg_endpoint + String(id);
    return fetch(url, { credentials: 'omit' })
      .then(r => {
        if (!r.ok) throw new Error(`SVG fetch failed: ${id}`);
        return r.text();
      })
      .then(text => {
        svgs.set(id, text);
        return text;
      });
  }

  async function svgToBitmap(svgText) {
    const blob = new Blob([svgText], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    try {
      const img = new Image();
      img.decoding = 'async';
      img.src = url;
      await img.decode();
      const bmp = await createImageBitmap(img);
      return bmp;
    } finally {
      URL.revokeObjectURL(url);
    }
  }

  async function ensureBitmap(id) {
    if (bitmaps.has(id)) return bitmaps.get(id);
    const svg = await fetchSvg(id);
    const bmp = await svgToBitmap(svg);
    bitmaps.set(id, bmp);
    return bmp;
  }

  function drawTile(bmp, x, y, size, alpha, grayscale) {
    ctx.save();
    ctx.globalAlpha = alpha;
    ctx.filter = grayscale ? 'grayscale(1)' : 'none';
    ctx.drawImage(bmp, x, y, size, size);
    ctx.restore();
  }

  function renderBase() {
    if (!ids.length) return;

    const off = document.createElement('canvas');
    off.width = canvas.width;
    off.height = canvas.height;
    const octx = off.getContext('2d', { alpha: true });
    octx.setTransform(DPR, 0, 0, DPR, 0, 0);

    // background is white via CSS; canvas stays transparent.
    octx.clearRect(0, 0, canvas.width, canvas.height);
    octx.globalAlpha = 0.7;
    octx.filter = 'grayscale(1)';

    const n = ids.length;
    const total = cols * rows;

    for (let i = 0; i < total; i++) {
      const id = ids[seededIndex(i, n)];
      const bmp = bitmaps.get(id);
      if (!bmp) continue;
      const x = (i % cols) * tile;
      const y = Math.floor(i / cols) * tile;
      octx.drawImage(bmp, x, y, tile, tile);
    }

    baseBitmap = off;
  }

  function render() {
    if (!baseBitmap) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw base
    ctx.drawImage(baseBitmap, 0, 0, baseBitmap.width / DPR, baseBitmap.height / DPR);

    // Draw hovered tile in full color
    if (hoverCell >= 0 && ids.length) {
      const n = ids.length;
      const id = ids[seededIndex(hoverCell, n)];
      const bmp = bitmaps.get(id);
      if (bmp) {
        const x = (hoverCell % cols) * tile;
        const y = Math.floor(hoverCell / cols) * tile;
        drawTile(bmp, x, y, tile, 1, false);
      }
    }
  }

  function cellFromEvent(ev) {
    const rect = canvas.getBoundingClientRect();
    const x = clamp(ev.clientX - rect.left, 0, rect.width - 1);
    const y = clamp(ev.clientY - rect.top, 0, rect.height - 1);
    const cx = Math.floor(x / tile);
    const cy = Math.floor(y / tile);
    return cy * cols + cx;
  }

  function showMagnifier(ev, cell) {
    if (!ids.length) return;
    const id = ids[seededIndex(cell, ids.length)];
    const svg = svgs.get(id);
    if (svg) {
      magImg.innerHTML = svg;
      // Force the embedded svg to fill the box.
      const svgEl = magImg.querySelector('svg');
      if (svgEl) {
        svgEl.setAttribute('width', '100%');
        svgEl.setAttribute('height', '100%');
      }
    } else {
      magImg.textContent = '';
    }

    magLabel.textContent = `#${id}`;

    const rect = container.getBoundingClientRect();
    const mx = clamp(ev.clientX - rect.left + 16, 16, rect.width - 16);
    const my = clamp(ev.clientY - rect.top - 16, 16, rect.height - 16);

    magnifier.style.transform = `translate(${mx}px, ${my}px)`;
    magnifier.classList.add('is-on');
  }

  function hideMagnifier() {
    magnifier.classList.remove('is-on');
  }

  function goToPunk(cell) {
    if (!ids.length) return;
    const id = ids[seededIndex(cell, ids.length)];
    window.location.href = `/${id}/`;
  }

  async function init() {
    setCanvasSize();

    // Load bitmaps (small set).
    await Promise.all(ids.map(async (id) => {
      try { await ensureBitmap(id); } catch (e) { /* ignore */ }
    }));

    renderBase();
    render();

    // Events
    let raf = 0;

    function onMove(ev) {
      const cell = cellFromEvent(ev);
      if (cell === hoverCell) return;
      hoverCell = cell;
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => {
        render();
        showMagnifier(ev, cell);
      });
    }

    function onLeave() {
      hoverCell = -1;
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => {
        render();
        hideMagnifier();
      });
    }

    function onClick(ev) {
      const cell = cellFromEvent(ev);
      goToPunk(cell);
    }

    // Pointer events (mouse + touch)
    canvas.addEventListener('pointermove', onMove);
    canvas.addEventListener('pointerleave', onLeave);
    canvas.addEventListener('pointerdown', onClick);

    // Resize
    window.addEventListener('resize', () => {
      setCanvasSize();
      renderBase();
      render();
    }, { passive: true });
  }

  init().catch(() => {});
})();