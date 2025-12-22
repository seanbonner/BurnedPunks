# SB Punks Registry (BurnedPunks / MuseumPunks)
Version: 0.2.0

This plugin is the shared “registry + front-end” layer for the BurnedPunks site now, and later the MuseumPunks sister site. It provides:
- A custom post type for punks
- A front-page mosaic grid
- A /the-punks/ index page
- Clean numeric permalinks like `/5449/`
- A redesigned single-punk page layout driven by CPT meta fields (not block formatting)

---

## What this plugin DOES (current behavior)

### 1) Custom Post Type
- CPT: `sb_punk` (shown as “Punks” in WP admin)
- Numeric slugs/titles are supported and intended (e.g. `5449`).

### 2) Permalinks
- Individual punk pages are routed as:
  - `https://yoursite.com/####/`
- The plugin forces these numeric permalinks for `sb_punk` posts.

### 3) Front page mosaic (shortcode)
- Shortcode: `[sb_punks_home]`
- Purpose: renders the homepage layout (logo area + grey mosaic grid).
- Mosaic behavior:
  - Mostly grey blocks
  - Each punk appears once (no repeats)
  - Positions are randomized per load but distributed across the grid
  - Punks are placed in ascending numeric order when scanning the grid (lowest appears “earlier” than highest)
  - Punk tiles are grayscale by default; hover shows color
- The “About” logo link is controlled via plugin settings.

### 4) Punks index page
- Route: `/the-punks/` is hard-handled by the plugin (not the theme “posts page”).
- Template shows a grid of all punk posts.
- Sorting:
  - Intended: newest burn first, based on the “Burn date” meta field (`YYYY-MM-DD`).

### 5) Single punk page redesign (new layout)
- Template: renders a two-column layout (desktop) matching the design mock:
  - Big punk number (from the title)
  - Burn type label (ACCIDENTAL/INTENTIONAL)
  - A facts block (claimer/burner/burned/final location/V1)
  - Story text (the actual post content)
- Image behavior:
  - Uses the post featured image (or first image in content as fallback)
  - Grayscale by default; hover shows color
  - Image links to the official CryptoPunks details page for that punk number
- Mobile:
  - Image first, then text below

### 6) CPT meta fields (the “Punk Details” boxes)
These are the canonical fields used by the single template:

**Core**
- Punk # (kept in sync with title/slug)
- Burn type: accidental / intentional
- Burn date: `YYYY-MM-DD` (used for index ordering)

**Participants (wallet + optional name)**
- Claimer wallet + optional name
- Burner wallet + optional name
- Final location wallet + optional name  
  (If name exists, display name; otherwise display wallet. In both cases link to cryptopunks.app account page.)

**V1 status**
- Checkbox: “V1 Wrapped”
  - If checked: displays “Wrapped” linked to OpenSea (auto-built from punk number)
  - If not checked: displays “Unwrapped” with no link

### 7) Legacy content migration helper
If a punk’s meta fields are empty, the plugin will attempt to parse the old “data blob” in post content (the `<h4>…</h4>` section) and fill:
- Claimer wallet/name
- Burner wallet/name
- Final location wallet/name
- V1 wrapped/unwrapped (basic detection)

This only fills missing fields and does not overwrite fields you’ve edited.

### 8) Admin cleanup for this CPT
- Disables block editor for `sb_punk` (uses classic editor)
- Removes excerpt/comments/trackbacks support for `sb_punk`

---

## What this plugin DOES NOT DO (yet)

### 1) No on-chain image rendering for single pages
- The single template currently uses featured images / uploaded JPGs.
- We intentionally avoided live on-chain rendering due to reliability/perf.
- A future enhancement could add a “Generate/refresh on-chain image” button that saves the result to the media library/featured image (best of both worlds).

### 2) No automated chain/subgraph importing (for this phase)
- We are not currently fetching claim/burn/wallet data from contracts/subgraphs.
- For BurnedPunks, the “facts” are treated as curated data entered/verified by hand.
- Import can be added later once MuseumPunks requirements are finalized.

### 3) No MuseumPunks-specific fields yet
- MuseumPunks will require additional fields (museum name, collection, acquisition context, exhibition references, etc.).
- Those fields are not implemented in 0.2.0.

### 4) No OpenSea existence checks
- We do NOT query OpenSea to verify wrapped status.
- Wrapped status is a checkbox; link is derived automatically if checked.

---

## Settings
WP Admin → Settings → **SB Punks Registry**
- Site mode: BurnedPunks / MuseumPunks (placeholder for later; currently mostly informational)
- About URL
- Logo image URL (default)
- Logo image URL (hover)

---

## Required pages / usage

### Homepage
- Create/edit your front page and include:
  - `[sb_punks_home]`
- The plugin adds a special body class (`sbpr-front`) so the CSS can hide theme chrome and control layout.

### Punks index
- `/the-punks/` is reserved and rendered by the plugin automatically.

---

## Upgrade / troubleshooting notes

### After updating the plugin
Always do:
- WP Admin → Settings → Permalinks → **Save** (once)
This flushes rewrite rules so numeric permalinks and `/the-punks/` resolve correctly.

### If a punk page is not using the new layout
Confirm:
- You are editing a **Punks (sb_punk)** item, not an old WordPress Post.
- The punk has a numeric title/slug.

### If ordering on /the-punks/ looks wrong
- Ensure each punk has “Burn date” filled in meta as `YYYY-MM-DD`.

---

## MuseumPunks plan (later)
When cloning this plugin for MuseumPunks (or using the same codebase with “mode”):
- Add museum-specific meta fields and rendering rules.
- Ensure museum mode never shows “burner/burn” semantics.
- Keep numeric permalinks and the general grid system consistent across both sites.

---

## Developer notes
- Templates live in: `templates/`
  - `single-sb_punk.php`
  - `the-punks.php`
- Assets live in: `assets/`
  - `sbpr.css`
  - `sbpr.js`

Contracts referenced for link building:
- CryptoPunks wrapper contract (OpenSea link building):
  - `0x282BDD42f4eb70e7A9D9F40c8fEA0825B7f68C5D`
- Official details/account pages are on `cryptopunks.app`

"""
