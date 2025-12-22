(function(){
  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  function shuffle(arr){
    const a = arr.slice();
    for(let i=a.length-1;i>0;i--){
      const j = Math.floor(Math.random()*(i+1));
      [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
  }

  function sampleEmptyPositions(total, emptyCount){
    const idxs = Array.from({length: total}, (_, i) => i);
    for(let i=0;i<emptyCount;i++){
      const j = i + Math.floor(Math.random()*(total - i));
      [idxs[i], idxs[j]] = [idxs[j], idxs[i]];
    }
    return new Set(idxs.slice(0, emptyCount));
  }

  function buildGrid(grid){
    const itemsRaw = grid.getAttribute('data-sbpr-items') || '[]';
    let items;
    try { items = JSON.parse(itemsRaw); } catch(e){ items = []; }
    if(!Array.isArray(items) || items.length === 0) return;

    const tile = 96;
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

    // Target ~50% empties, but NEVER create repeats before every punk appears once
    // (when the grid has enough slots).
    const desiredEmpty = Math.floor(total * 0.5);
    const maxEmpty = Math.max(0, total - items.length); // keep at least one slot per punk
    const emptyCount = Math.min(desiredEmpty, maxEmpty);

    const emptyPos = sampleEmptyPositions(total, emptyCount);

    const nonEmpty = total - emptyCount;
    let assign = [];

    if (nonEmpty <= items.length){
      // Not enough slots to show all punks: show a unique subset (no repeats) and no empties.
      // (This prevents the "4-5 of the same punk" issue on small screens.)
      /* no-op */
      assign = shuffle(items).slice(0, total);
      // If we cleared empties, nonEmpty becomes total effectively.
      // We'll render 'total' items below.
    } else {
      // Enough slots: ensure every punk appears once before any repeats.
      const firstPass = shuffle(items);
      assign = firstPass.slice();
      while(assign.length < nonEmpty){
        assign = assign.concat(shuffle(items));
      }
      assign = assign.slice(0, nonEmpty);
    }

    const frag = document.createDocumentFragment();
    let k = 0;

    for(let i=0;i<total;i++){
      if(emptyPos.has(i)){
        const s = document.createElement('span');
        s.className = 'sbpr-emptycell';
        frag.appendChild(s);
        continue;
      }

      const it = assign[k++];
      if(!it){
        // Fallback: if empties were disabled due to small screen logic.
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