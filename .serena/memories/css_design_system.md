# CSS Design System

## Token Layers

1. **tokens.css** — M3 design token definitions
   - Color: `--md-sys-color-primary`, `--md-sys-color-surface`, etc.
   - Typography: `--md-sys-typescale-display-medium-size`, etc.
   - Shape: `--md-sys-shape-corner-small/medium/large/extra-large/full`
   - Motion: `--md-sys-motion-duration-short2/medium2`, `--md-sys-motion-easing-standard`
   - Elevation: `--md-sys-elevation-level-0` through `level-5`

2. **base.css** — Reset, base elements, utility classes

3. **components.css** — M3 component primitives
   - Cards: `.md-card-elevated`, `.md-card-filled`, `.md-card-outlined`
   - Chips: `.md-chip-filter`, `.md-chip-assist`
   - Buttons: `.md-btn-filled`, `.md-btn-outlined`, `.md-btn-text`
   - Text fields, dialogs, etc.

4. **theme.css** — Theme-specific compositions
   - App bar: `.app-bar`
   - Hero: `.hero-section` (floating blobs animation)
   - Posts grid: `.posts-grid`, `.post-card`
   - Single post: `.post-title`, `.post-content`, `.toc-container`
   - Comments: `.comment-item`, `.comment-body`, `.comment-header`
   - Footer: site footer styles
   - Responsive breakpoints at bottom of file

## Dark Mode

- Implemented via `[data-theme="dark"]` attribute on `<html>`
- Tokens automatically switch between light/dark palettes
- `color-engine.js` generates dynamic color from primary seed
- Toggle: `theme-toggle.js` cycles light → dark → system

## Naming Convention

- BEM: `.block__element--modifier`
- M3 tokens: `--md-sys-{category}-{token-name}`
- CSS classes map to M3 spec where possible
