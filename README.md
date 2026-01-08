# BurnedPunks

A WordPress site documenting CryptoPunks that have been burned (sent to unrecoverable addresses).

## Project Structure

```
wp-content/
├── plugins/
│   └── sb-punks-registry/       # Core plugin for burned punk registry
│       ├── templates/           # Custom page templates
│       └── assets/              # CSS and JS
└── themes/
    ├── minimalio/               # Base theme
    └── minimalio-child/         # Active child theme
```

## Deployment

This project uses GitHub Actions for automatic deployment to Hostinger via FTP.

**Triggers:** Push to `main` branch (only when `wp-content/themes/` or `wp-content/plugins/` change)

**Required Secrets:**
- `FTP_SERVER` - Hostinger FTP server
- `FTP_USERNAME` - FTP username
- `FTP_PASSWORD` - FTP password

**Deploy Target:** `burnedpunks.com`

## Custom Plugin

### SB Punks Registry (v0.3.5)

Registry plugin for tracking burned CryptoPunks.

**Custom Post Type:**
- `sb_punk` - Individual punk entries with numeric permalinks (`/5449/`)

**Meta Fields:**

| Field | Description |
|-------|-------------|
| `_sbpr_punk_id` | Punk number (0-9999), synced with title/slug |
| `_sbpr_intent` | intentional / accidental |
| `_sbpr_burn_date` | Date burned (YYYY-MM-DD) |
| `_sbpr_claimer_wallet` | Original claimer wallet |
| `_sbpr_claimer_name` | Claimer name (if known) |
| `_sbpr_claim_date` | Day claimed (1-30 June 2017) |
| `_sbpr_burner_wallet` | Wallet that burned the punk |
| `_sbpr_burner_name` | Burner name (if known) |
| `_sbpr_final_wallet` | Final resting place (burn address) |
| `_sbpr_final_name` | Final location label |
| `_sbpr_v1_wrapped` | V1 wrapper status |

**Shortcodes:**
- `[sb_punks_home]` - Homepage mosaic with logo header
- `[sb_punks_index]` - Grid index of all burned punks (sorted by burn date)

**Custom Pages:**
- `/the-punks/` - Full burned punk index

**Settings:**
- Site mode (burned/museum)
- About URL (logo link destination)
- Logo default/hover images

**Features:**
- Numeric permalinks (`/5449/` for punk #5449)
- Automatic punk image generation from Larva Labs
- Nearest-neighbor scaling for crisp pixel art (24x24 -> 480x480)
- Skips thumbnail generation for punk images
- Burn tracking (date, intent, participants)
- Original claimer tracking
- V1 Wrapped Punk support (links to OpenSea wrapper contract)
- Legacy content migration (parses existing post content into meta fields)
- Comments/trackbacks disabled

**Image Generation:**
- Fetches official punk images from Larva Labs
- Scales using nearest-neighbor interpolation to preserve pixel art
- Automatically sets as featured image when punk ID is entered

## URL Structure

| Content | URL Pattern |
|---------|-------------|
| Individual Punk | `/{punk-number}/` |
| Punks Index | `/the-punks/` |

## External Links

The plugin generates links to:
- CryptoPunks.app - Punk details and wallet profiles
- OpenSea - V1 Wrapped Punks (via wrapper contract `0x282BDD42f4eb70e7A9D9F40c8fEA0825B7f68C5D`)

## Requirements

- WordPress 5.0+
- PHP 7.4+
- GD library (required for punk image scaling)

## License

Private/Proprietary

---

*Last updated: January 8, 2026*
