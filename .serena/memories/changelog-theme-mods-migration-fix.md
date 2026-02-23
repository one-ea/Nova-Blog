# Fix: Theme Mods Migration (flavor-theme-1 → flavor-theme)

## Date: 2026-02-23

## Problem
After a theme deployment, WordPress installed the theme as a NEW directory `flavor-theme` instead of upgrading the existing `flavor-theme-1`. This caused all Customizer settings (seed color, social links, homepage toggles, etc.) to be lost because WordPress stores `theme_mods` per theme slug (`theme_mods_flavor-theme` vs `theme_mods_flavor-theme-1`).

The user reported: "首页的推荐块怎么变回去了" — the featured block looked different because the M3 color tokens were regenerated from the default seed color instead of the user's custom warm brown `#91593b`.

## Root Cause
- WordPress stores Customizer settings in `wp_options` table as `theme_mods_{directory_name}`
- When theme was uploaded as zip, WordPress created `flavor-theme` (new) instead of upgrading `flavor-theme-1` (existing)
- After activating `flavor-theme`, all settings reverted to defaults since `theme_mods_flavor-theme` was empty
- Old settings remained orphaned in `theme_mods_flavor-theme-1`

## Fix Applied

### `functions.php` — One-time migration function
Added an `after_setup_theme` hook (priority 1) that:
1. Checks if migration already done via `flavor_mods_migrated_v1` option flag
2. Reads `theme_mods_flavor-theme-1` from database
3. Merges old settings into current theme mods (without overwriting existing new settings)
4. Sets flag to prevent re-execution

```php
add_action('after_setup_theme', function () {
    if (get_option('flavor_mods_migrated_v1')) return;
    $old_mods = get_option('theme_mods_flavor-theme-1');
    if (!$old_mods || !is_array($old_mods)) {
        update_option('flavor_mods_migrated_v1', true);
        return;
    }
    foreach ($old_mods as $key => $value) {
        if (false === get_theme_mod($key)) {
            set_theme_mod($key, $value);
        }
    }
    update_option('flavor_mods_migrated_v1', true);
}, 1);
```

### Version bump
`FLAVOR_VERSION` updated from `2.15.0` to `2.16.0`.

## Deployment
- Zipped and uploaded via WordPress admin → "使用上传的版本替换已安装版本"
- This time WordPress correctly recognized the existing `flavor-theme` directory and performed an upgrade (not a new install)
- Migration triggered on first homepage visit after deployment

## Lesson Learned
- **Always deploy via "replace/upgrade"** — never let WordPress create a new `-1` suffixed directory
- When theme slug changes, ALL Customizer settings are lost
- The migration function is idempotent and safe to keep in codebase (guarded by option flag)
- Consider cleaning up `flavor-theme-1` directory from server to avoid future confusion
