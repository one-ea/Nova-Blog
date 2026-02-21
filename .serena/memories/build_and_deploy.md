# Build & Deploy Workflow

## Build

```bash
cd flavor-theme && npm run build
```

- Uses `build.mjs` (esbuild)
- Minifies CSS + JS from `assets/css/` and `assets/js/` → `assets/dist/`
- Outputs ~12 files

## Version Bumping (required for cache busting)

Two files must be updated in sync:
1. `flavor-theme/functions.php`: `define('FLAVOR_VERSION', 'x.y.z');`
2. `flavor-theme/style.css`: `Version: x.y.z` (in the WordPress theme header comment)

If version is not bumped, browsers will serve cached old CSS/JS.

## Packaging

```bash
cd /home/easy/Nova-Blog && zip -r flavor-theme-upload.zip flavor-theme/ \
  -x "flavor-theme/node_modules/*" "flavor-theme/.git/*" \
  "flavor-theme/assets/css/*" "flavor-theme/assets/js/*"
```

- Excludes source CSS/JS (only ships `assets/dist/`)
- Excludes `node_modules` and `.git`

## Deploying

1. Navigate to WordPress admin: `/wp-admin/theme-install.php?upload`
2. Click "上传主题" → select ZIP → "立即安裝"
3. WordPress shows version comparison → click "使用上传的版本替换已安装版本"
4. Verify on live site (check CSS version in source to confirm cache bust)

## Site Info

- URL: https://www.roxilodz.eu.org/
- Admin: WordPress 6.9.1
- Theme: flavor-theme (Flavor)
- Current version: 2.6.5
