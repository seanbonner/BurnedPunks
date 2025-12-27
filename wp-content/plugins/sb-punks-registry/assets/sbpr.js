(function(){
  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  function uniquePositionsStratified(total, n){
    // Pick ~evenly distributed positions across [0,total) by taking 1 from each segment.
    const chosen = new Set();
    const positions = [];
    if(n <= 0) return positions;

    for(let i=0;i<n;i++){
      const start = Math.floor(i * total / n);
      const end   = Math.max(start, Math.floor((i + 1) * total / n) - 1);

      let pick = start;
      if(end > start){
        pick = start + Math.floor(Math.random() * (end - start + 1));
      }

      // Try within segment first.
      let found = -1;
      const segLen = end - start + 1;
      for(let k=0;k<segLen;k++){
        const idx = start + ((pick - start + k) % segLen);
        if(!chosen.has(idx)){
          found = idx;
          break;
        }
      }

      // Fallback: global scan.
      if(found === -1){
        for(let idx=0; idx<total; idx++){
          if(!chosen.has(idx)){
            found = idx;
            break;
          }
        }
      }

      if(found !== -1){
        chosen.add(found);
        positions.push(found);
      }
    }

    positions.sort((a,b)=>a-b);
    return positions;
  }

  function buildGrid(grid){
    const itemsRaw = grid.getAttribute('data-sbpr-items') || '[]';
    let items;
    try { items = JSON.parse(itemsRaw); } catch(e){ items = []; }
    if(!Array.isArray(items) || items.length === 0) return;

    // Order by punk number ascending (so lower IDs appear earlier in the reading order)
    items = items.slice().sort((a,b)=> (parseInt(a.num,10)||0) - (parseInt(b.num,10)||0));

    const tile = 96;
    const gap  = 6;

    const w = Math.max(320, window.innerWidth);
    const cols = Math.max(3, Math.floor((w + gap) / (tile + gap)));

    const header = document.querySelector('.sbpr-header');
    const headerH = header ? header.getBoundingClientRect().height : 0;
    const targetH = Math.max(320, window.innerHeight - headerH - 48);
    const rows = Math.max(2, Math.floor((targetH + gap) / (tile + gap)));
    const total = cols * rows;

    grid.style.setProperty('--sbpr-tile', tile + 'px');
    grid.style.setProperty('--sbpr-gap', gap + 'px');
    grid.style.setProperty('--sbpr-cols', String(cols));

    // Place each punk once; everything else is a grey block.
    const n = Math.min(items.length, total);
    const positions = uniquePositionsStratified(total, n);

    const posToItem = new Map();
    for(let i=0;i<positions.length;i++){
      posToItem.set(positions[i], items[i]);
    }

    const frag = document.createDocumentFragment();

    for(let i=0;i<total;i++){
      const it = posToItem.get(i);
      if(!it){
        const s = document.createElement('span');
        s.className = 'sbpr-emptycell';
        frag.appendChild(s);
        continue;
      }

      const a = document.createElement('a');
      a.className = 'sbpr-tile';
      a.href = it.href;
      a.setAttribute('aria-label', 'Punk ' + it.num);

      if(it.thumb){
        const img = document.createElement('img');
        img.className = 'sbpr-tile__img';
        img.src = it.thumb;
        img.alt = '';
        img.decoding = 'async';
        // Only ~12 images, so eager loading keeps hover smooth.
        img.loading = 'eager';
        a.appendChild(img);
      }
      frag.appendChild(a);
    }

    grid.innerHTML = '';
    grid.appendChild(frag);
  }

  ready(function(){
    const grid = document.querySelector('.sbpr-mosaic__grid[data-sbpr-items]');
    if(grid) buildGrid(grid);

    let t = null;
    window.addEventListener('resize', function(){
      if(!grid) return;
      if(t) clearTimeout(t);
      t = setTimeout(function(){ buildGrid(grid); }, 150);
    });
  });
})();