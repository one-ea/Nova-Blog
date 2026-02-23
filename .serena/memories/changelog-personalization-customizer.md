# Flavor Theme 个性化 Customizer 设置

**日期**: 2026-02-23
**版本**: 基于 v2.15.0 (functions.php FLAVOR_VERSION)

## 变更概要

为 Flavor Theme 新增"个性化"Customizer 面板，包含 4 大类 11 个设置项，大幅提升主题可定制性。

## 修改文件清单

### 1. `inc/customizer.php`
- 新增 `flavor_personalization` Panel (priority 32)
- **字体与排版 Section** (`flavor_typography`):
  - `flavor_font_family`: select — system(默认) / serif / rounded
  - `flavor_font_scale`: select — compact / standard(默认) / large
- **视觉风格 Section** (`flavor_visual`):
  - `flavor_corner_style`: select — rounded(默认) / sharp / pill
  - `flavor_card_style`: select — elevated(默认) / outlined / filled
  - `flavor_content_density`: select — comfortable(默认) / compact / spacious
- **首页模块 Section** (`flavor_homepage_modules`):
  - `flavor_show_hero`: checkbox (默认 true)
  - `flavor_show_featured`: checkbox (默认 true)
  - `flavor_show_category_filter`: checkbox (默认 true)
- **动画与交互 Section** (`flavor_animations`):
  - `flavor_enable_animations`: checkbox (默认 true)
  - `flavor_enable_scroll_animations`: checkbox (默认 true)

### 2. `functions.php`
- 新增 `flavor_personalization_body_class()` — body_class 过滤器，按设置值添加 CSS 类 (font-serif, scale-compact, corners-sharp, cards-outlined, density-compact, no-animations 等)
- 新增 `flavor_personalization_css()` — wp_head priority 3，输出 `<style id="flavor-personalization">` 覆盖 CSS 自定义属性（字体系列、字体缩放、圆角风格）
- 新增 `flavor_card_class()` — 返回 md-card-elevated / md-card-outlined / md-card-filled
- 改造 `flavor_exclude_sticky_from_main()` — 当 `flavor_show_featured` 为 false 时不排除置顶文章

### 3. `inc/enqueue.php`
- `flavor_preload_assets()` — Google Fonts URL 动态拼接，serif 追加 Noto Serif SC，rounded 追加 Nunito
- `flavor_enqueue_scripts()` — 新增 `wp_localize_script('flavor-scroll-enhance', 'flavorConfig', ...)` 传递动画开关

### 4. `assets/css/theme.css` (+ dist 同步)
- 新增 `/* Personalization Variants */` 区块：字体覆盖、密度变体、动画禁用、卡片风格覆盖

### 5. `index.php`
- Hero / Featured / Category Filter 三模块条件渲染 (`get_theme_mod`)

### 6. `template-parts/content-card.php` + `content-featured.php`
- 硬编码 `md-card-elevated` → `flavor_card_class()` 动态类名

### 7. `assets/js/scroll-enhance.js`
- 读取 `window.flavorConfig`，三重守卫（prefers-reduced-motion → enableAnimations → enableScrollAnimations）

## 设计原则
- **Zero-overhead**: 默认值时不添加类、不输出额外 CSS
- **双层覆盖**: CSS 自定义属性 (tokens 级) + body class 选择器 (组件级)
- **渐进增强**: JS 动画有三重 fallback 守卫
