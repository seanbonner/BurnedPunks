
(function(){
  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  function clamp(v, min, max){ return Math.max(min, Math.min(max, v)); }

  function buildGrid(grid){
    const itemsRaw = grid.getAttribute('data-sbpr-items') || '[]';
    let items;
    try { items = JSON.parse(itemsRaw); } catch(e){ items = []; }
    if(!Array.isArray(items) || items.length === 0) return;

    const mode = grid.getAttribute('data-sbpr-mode') || 'home';

    const tile = 12;
    const gap  = 2;

    // Use viewport width for full-bleed accuracy.
    const w = Math.max(320, window.innerWidth);
    const cols = Math.max(40, Math.floor((w + gap) / (tile + gap)));

    // Fill to bottom of viewport under header.
    const header = document.querySelector('.sbpr-header');
    const headerH = header ? header.getBoundingClientRect().height : 0;
    const targetH = Math.max(320, window.innerHeight - headerH - 24);
    const rows = Math.max(20, Math.ceil((targetH + gap) / (tile + gap)));
    const total = cols * rows;

    grid.style.setProperty('--sbpr-tile', tile + 'px');
    grid.style.setProperty('--sbpr-gap', gap + 'px');
    grid.style.setProperty('--sbpr-cols', String(cols));

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

    function positionMag(e){
      if(!mag.classList.contains('is-on')) return;
      const pad = 18;
      const w = mag.offsetWidth || 168;
      const h = mag.offsetHeight || 168;
      let x = e.clientX + 18;
      let y = e.clientY + 18;

      // Flip if near right/bottom edge.
      x = clamp(x, pad, window.innerWidth - w - pad);
      y = clamp(y, pad, window.innerHeight - h - pad);

      mag.style.left = x + 'px';
      mag.style.top  = y + 'px';
    }

    document.addEventListener('mousemove', positionMag, { passive: true });

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
      positionMag(e);
    });

    document.addEventListener('mouseout', function(e){
      const leaving = e.target.closest && e.target.closest('.sbpr-tile');
      if(leaving) clearMag();
    });
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

    initMagnifier();
  });
})();
