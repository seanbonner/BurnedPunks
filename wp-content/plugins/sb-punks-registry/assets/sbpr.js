
(function(){
  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  function buildGrid(grid){
    const itemsRaw = grid.getAttribute('data-sbpr-items') || '[]';
    let items;
    try { items = JSON.parse(itemsRaw); } catch(e){ items = []; }
    if(!Array.isArray(items) || items.length === 0) return;

    const tile = 96; // 8x from the original 12px
    const gap  = 6;

    const w = Math.max(320, window.innerWidth);
    const cols = Math.max(3, Math.floor((w + gap) / (tile + gap)));

    const header = document.querySelector('.sbpr-header');
    const headerH = header ? header.getBoundingClientRect().height : 0;
    const targetH = Math.max(320, window.innerHeight - headerH - 24);
    const rows = Math.max(2, Math.ceil((targetH + gap) / (tile + gap)));
    const total = cols * rows;

    grid.style.setProperty('--sbpr-tile', tile + 'px');
    grid.style.setProperty('--sbpr-gap', gap + 'px');
    grid.style.setProperty('--sbpr-cols', String(cols));

    const frag = document.createDocumentFragment();

    for(let i=0;i<total;i++){
      // 50% empty cells
      if(Math.random() < 0.5){
        const s = document.createElement('span');
        s.className = 'sbpr-emptycell';
        frag.appendChild(s);
        continue;
      }

      const it = items[Math.floor(Math.random()*items.length)];
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
        // With large tiles + empties, most loads are fast; eager first row.
        img.loading = (i < cols) ? 'eager' : 'lazy';
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
