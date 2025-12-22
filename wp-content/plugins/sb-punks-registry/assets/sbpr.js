
(function(){
  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  function buildGrid(grid){
    let itemsRaw = grid.getAttribute('data-sbpr-items') || '[]';
    let mode = grid.getAttribute('data-sbpr-mode') || 'mosaic';
    let items;
    try { items = JSON.parse(itemsRaw); } catch(e){ items = []; }
    if(!Array.isArray(items) || items.length === 0) return;

    // Tile sizing: home should feel like "thousands". Keep it small.
    const tile = (mode === 'home') ? 12 : 18;
    const gap  = (mode === 'home') ? 2 : 2;

    // Calculate columns based on available width.
    const padX = 0; // already handled by CSS padding on grid
    const w = Math.max(320, grid.clientWidth - padX);
    const cols = Math.max(40, Math.floor((w + gap) / (tile + gap)));

    // Calculate rows to fill viewport to bottom (home), or a reasonable block (mosaic).
    let targetH;
    if(mode === 'home'){
      const header = document.querySelector('.sbpr-header');
      const headerH = header ? header.getBoundingClientRect().height : 0;
      targetH = Math.max(320, window.innerHeight - headerH - 24);
    } else {
      targetH = Math.max(420, Math.min(1200, window.innerHeight));
    }
    const rows = Math.max(20, Math.ceil((targetH + gap) / (tile + gap)));
    const total = cols * rows;

    grid.style.setProperty('--sbpr-tile', tile + 'px');
    grid.style.setProperty('--sbpr-gap', gap + 'px');
    grid.style.setProperty('--sbpr-cols', String(cols));
    grid.style.setProperty('--sbpr-rows', String(rows));

    // Build tiles with randomized selection to avoid banding.
    const frag = document.createDocumentFragment();
    for(let i=0;i<total;i++){
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
        img.loading = (i < cols*3) ? 'eager' : 'lazy';
        a.appendChild(img);
      }
      frag.appendChild(a);
    }
    grid.innerHTML = '';
    grid.appendChild(frag);
  }

  function initMagnifier(){
    const mag = document.querySelector('.sbpr-mag');
    const magInner = mag ? mag.querySelector('.sbpr-mag__inner') : null;
    if(!mag || !magInner) return;

    function clearMag(){
      mag.classList.remove('is-on');
      magInner.innerHTML = '';
    }

    document.addEventListener('mouseover', function(e){
      const a = e.target.closest && e.target.closest('.sbpr-tile');
      if(!a) return;
      const img = a.querySelector('img');
      if(!img || !img.src) return;

      magInner.innerHTML = '';
      const clone = img.cloneNode(true);
      clone.loading = 'eager';
      clone.style.filter = 'none';
      clone.style.opacity = '1';
      magInner.appendChild(clone);
      mag.classList.add('is-on');
    });

    document.addEventListener('mouseout', function(e){
      const leaving = e.target.closest && e.target.closest('.sbpr-tile');
      if(leaving) clearMag();
    });
  }

  ready(function(){
    const grids = document.querySelectorAll('.sbpr-mosaic__grid[data-sbpr-items]');
    grids.forEach(buildGrid);

    // Rebuild on resize (debounced)
    let t = null;
    window.addEventListener('resize', function(){
      if(t) clearTimeout(t);
      t = setTimeout(function(){
        grids.forEach(buildGrid);
      }, 150);
    });

    initMagnifier();
  });
})();
