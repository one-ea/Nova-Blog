# v2.18.0 — TOC 目录层级视觉优化

## 日期
2026-02-23

## 变更摘要
优化文章目录（Table of Contents）的视觉层级区分，从"仅缩进"改为"字号+字重+颜色+缩进"四维分级。

## 修改文件

### `assets/css/theme.css`
- `.toc-list` 添加 `border-left: 1px solid outline-variant` 统一竖线
- `.toc-link` 添加 `margin-left: -1px` 让激活高亮条覆盖竖线
- 新增 `.toc-item--level-0 > .toc-link` — h2 级别：`body-medium` 字号、500 字重、`on-surface` 深色、padding 8px 12px
- 新增 `.toc-item--level-0 + .toc-item--level-0` — h2 之间 4px 间距
- 新增 `.toc-item--level-1 > .toc-link` — h3 级别：`body-small` 字号、400 字重、`on-surface-variant` 灰色、padding-left 24px
- 新增 `.toc-item--level-2 > .toc-link` — h4 级别：`label-medium` 字号、400 字重、70% 透明度、padding-left 36px
- 激活态 `.toc-item--level-0 > .toc-link--active` 使用 600 字重
- hover 和 active 的 `color-mix()` 写法替代旧的 `/` 语法

### `assets/js/toc.js`
- 移除 `li.style.paddingLeft = ${indent * 16}px` 内联样式
- 缩进改由 CSS class `.toc-item--level-{N}` 控制

### `functions.php`
- FLAVOR_VERSION: 2.17.1 → 2.18.0

## 设计原则
- 四维视觉阶梯：字号、字重、颜色深浅、缩进距离
- 关注点分离：样式从 JS inline 迁移到 CSS class
- 左侧竖线作为统一视觉引导，激活项高亮条覆盖竖线
