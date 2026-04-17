# BurnedPunks

A registry of CryptoPunks that have been burned (sent to unrecoverable addresses).

Live at [burnedpunks.com](https://burnedpunks.com).

## Stack

Static site built with [Eleventy v3](https://www.11ty.dev/). Content lives in `.md` files. Punk images are SVGs fetched from the on-chain [CryptoPunksData contract](https://etherscan.io/address/0x16F5A35647D6F03D5D3da7b35409D65ba03aF3B2) and committed to the repo. Deploys via [Cloudflare Pages](https://pages.cloudflare.com/) on push to `main`.

The previous WordPress + Hostinger implementation is preserved on the `wp-legacy` branch for reference.

## Repo layout

```
punks/              One .md file per burned punk (filename = punk ID).
pages/              Top-level pages (homepage, /the-punks/, /about/, /faq/).
images/
├── logos/          Site logos (png, gif).
└── punks/          On-chain SVGs, one per punk (filename = punk ID).
_includes/layouts/  Nunjucks templates (base, punk).
_data/site.js       Site-wide config (title, external URLs).
css/style.css       All styles.
js/mosaic.js        Homepage mosaic layout (runs in browser).
scripts/
├── fetch-punk-svgs.mjs    Pulls SVGs from the CryptoPunksData contract.
└── import-narratives.mjs  Re-pulls verbatim narratives from the live WP site via defuddle (historical; no longer needed).
eleventy.config.mjs
```

## Local development

```
npm install
npm run serve
```

Opens `http://localhost:8080/` with live reload.

## Adding a new punk

1. Create `punks/{id}.md` with the frontmatter schema below.
2. Run `npm run fetch-svgs -- {id}` to pull the punk's SVG from chain into `images/punks/{id}.svg`.
3. Commit + push. Cloudflare Pages builds and deploys automatically.

### Punk frontmatter schema

```yaml
---
id: 5237                                    # required; matches filename
intent: intentional                         # accidental | intentional
burn_date: "2023-08-10"                     # required, YYYY-MM-DD (quoted)
claimer_wallet: "0x..."                     # required
claimer_name: "Optional Name"               # optional; falls back to short wallet
claim_date: 18                              # day of June 2017 (1–30)
burner_wallet: "0x..."                      # optional
burner_name: "Optional Name"                # optional
final_wallet: "0x..."                       # optional; where the punk ended up
final_name: "Optional Name"                 # optional
v1_wrapped: false                           # bool
---
Narrative body in markdown. Inline links encouraged.
```

## Editing content

Every page is a `.md` file under `punks/` or `pages/`. Edit, commit, push. Cloudflare Pages rebuilds on every push to `main` (takes ~30–60 seconds). Every branch and PR gets its own `*.pages.dev` preview URL.

## License

Content (prose, data, curation): [CC-BY 4.0](https://creativecommons.org/licenses/by/4.0/). See individual punk pages for sources.

Site code: MIT-adjacent; do what you like.
