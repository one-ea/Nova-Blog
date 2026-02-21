# Flavor Theme Architecture

## File Structure

```
flavor-theme/
├── style.css              # WP theme header (name, version, description)
├── functions.php          # Theme bootstrap: FLAVOR_VERSION, enqueues, includes
├── index.php              # Homepage: hero section + category chips + posts grid
├── single.php             # Single post: article + TOC sidebar + comments
├── archive.php            # Category/tag archive listing
├── search.php             # Search results page
├── 404.php                # Not found page
├── header.php             # <head> + app bar + nav + search overlay + theme toggle
├── footer.php             # Footer widgets + copyright
├── comments.php           # Comment list + reply form
├── searchform.php         # Search form partial
│
├── inc/
│   ├── enqueue.php        # CSS/JS registration with FLAVOR_VERSION cache busting
│   ├── customizer.php     # WP Customizer settings (colors, layout options)
│   ├── seo.php            # Meta tags, Open Graph, structured data
│   ├── walker-comment.php # Custom comment walker (Flavor_Walker_Comment class)
│   ├── widgets.php        # Custom widget areas
│   ├── block-patterns.php # Gutenberg block patterns
│   └── block-styles.php   # Gutenberg block style variants
│
├── template-parts/
│   ├── content-card.php       # Post card (grid layout)
│   ├── content-featured.php   # Featured/sticky post card (overlay layout)
│   ├── content-list.php       # List layout card
│   ├── author-card.php        # Author bio card
│   └── search-bar.php         # Search bar component
│
├── assets/
│   ├── css/
│   │   ├── tokens.css      # M3 design tokens (colors, typography, shapes, motion)
│   │   ├── base.css        # CSS reset + base typography + utilities
│   │   ├── components.css  # M3 components (cards, chips, buttons, dialogs)
│   │   └── theme.css       # Theme-specific styles (hero, posts, single, comments)
│   ├── js/
│   │   ├── toc.js          # Table of contents (IntersectionObserver)
│   │   ├── search.js       # Search overlay logic
│   │   ├── navigation.js   # Mobile nav drawer
│   │   ├── ripple.js       # M3 ripple effect
│   │   ├── scroll-enhance.js # Scroll-triggered animations
│   │   ├── theme-toggle.js # Light/dark/system theme switching
│   │   └── color-engine.js # Dynamic color from primary seed
│   └── dist/               # Built output (DO NOT edit)
│
├── build.mjs              # esbuild config
├── package.json           # Node dependencies (esbuild only)
└── sw.js                  # Service worker for offline support
```

## Key Patterns

- **Cache busting**: `FLAVOR_VERSION` in `functions.php` → used as `?ver=` param for all enqueued assets
- **CSS architecture**: tokens → base → components → theme (loaded in this order)
- **M3 tokens**: All colors/typography/shapes use `--md-sys-*` custom properties
- **BEM naming**: `.block__element--modifier` pattern for all custom CSS classes
- **Template hierarchy**: Standard WordPress template loading (index→archive→single→page)
