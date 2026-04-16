(function () {
  function ready(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  function stratifiedPositions(total, n) {
    const chosen = new Set();
    const positions = [];
    if (n <= 0) return positions;
    for (let i = 0; i < n; i++) {
      const start = Math.floor((i * total) / n);
      const end = Math.max(start, Math.floor(((i + 1) * total) / n) - 1);
      let pick = start;
      if (end > start) pick = start + Math.floor(Math.random() * (end - start + 1));
      let found = -1;
      const segLen = end - start + 1;
      for (let k = 0; k < segLen; k++) {
        const idx = start + ((pick - start + k) % segLen);
        if (!chosen.has(idx)) { found = idx; break; }
      }
      if (found === -1) {
        for (let idx = 0; idx < total; idx++) {
          if (!chosen.has(idx)) { found = idx; break; }
        }
      }
      if (found !== -1) { chosen.add(found); positions.push(found); }
    }
    positions.sort((a, b) => a - b);
    return positions;
  }

  function buildGrid(grid) {
    let items;
    try { items = JSON.parse(grid.getAttribute("data-mosaic-items") || "[]"); }
    catch { items = []; }
    if (!Array.isArray(items) || items.length === 0) return;

    items = items.slice().sort((a, b) => (parseInt(a.num, 10) || 0) - (parseInt(b.num, 10) || 0));

    const tile = 96;
    const gap = 6;
    const w = Math.max(320, window.innerWidth);
    const cols = Math.max(3, Math.floor((w + gap) / (tile + gap)));

    const header = document.querySelector(".home-hero__header");
    const headerH = header ? header.getBoundingClientRect().height : 0;
    const targetH = Math.max(320, window.innerHeight - headerH - 48);
    const rows = Math.max(2, Math.floor((targetH + gap) / (tile + gap)));
    const total = cols * rows;

    grid.style.setProperty("--mosaic-tile", tile + "px");
    grid.style.setProperty("--mosaic-gap", gap + "px");
    grid.style.setProperty("--mosaic-cols", String(cols));

    const n = Math.min(items.length, total);
    const positions = stratifiedPositions(total, n);

    const posToItem = new Map();
    for (let i = 0; i < positions.length; i++) posToItem.set(positions[i], items[i]);

    const frag = document.createDocumentFragment();
    for (let i = 0; i < total; i++) {
      const it = posToItem.get(i);
      if (!it) {
        const s = document.createElement("span");
        s.className = "mosaic__empty";
        frag.appendChild(s);
        continue;
      }
      const a = document.createElement("a");
      a.className = "mosaic__tile";
      a.href = it.href;
      a.setAttribute("aria-label", "Punk " + it.num);
      if (it.thumb) {
        const img = document.createElement("img");
        img.className = "mosaic__tile-img";
        img.src = it.thumb;
        img.alt = "";
        img.decoding = "async";
        img.loading = "eager";
        a.appendChild(img);
      }
      frag.appendChild(a);
    }
    grid.innerHTML = "";
    grid.appendChild(frag);
  }

  ready(function () {
    const grid = document.querySelector(".mosaic__grid[data-mosaic-items]");
    if (!grid) return;
    buildGrid(grid);
    let t = null;
    window.addEventListener("resize", function () {
      if (t) clearTimeout(t);
      t = setTimeout(function () { buildGrid(grid); }, 150);
    });
  });
})();
