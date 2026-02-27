# Flavor Theme v2.19.0 — Surface Tint Elevation 系统

## 发布日期
2026-02-25

## 变更概述
实现 M3 Surface Tint Elevation 系统（TOP1）+ Post Card 暗色模式优化（TOP9）

## 修改文件

### tokens.css (源码 + dist)
- 新增 6 级 tint overlay token（tint-0 到 tint-5）
- Light mode: 全部 `transparent`（阴影为主层级指示器）
- Dark mode: 使用 `color-mix(in srgb, surface-tint X%, transparent)` — 5%/8%/11%/12%/14%
- Dark mode 阴影减弱：opacity 从 0.3/0.15 降至 0.15/0.08
- 同时应用到 `[data-theme="dark"]` 和 `@media (prefers-color-scheme: dark) [data-theme="auto"]`

### theme.css (源码 + dist)
- `.app-bar.scrolled`: 添加 tint-2 background-image
- `.posts-hero .post-card`: background 拆分为 background-color + tint-1 background-image
- `.toc-container`: 添加 tint-1 background-image
- `.site-footer`: 添加 tint-2 background-image
- `.nav-drawer`: 添加 tint-1 background-image
- `.search-overlay`: 添加 tint-3 background-image
- `.post-card`: border 改用 60% 透明度，添加 tint-1，hover 升至 tint-2
- `.post-card:hover`: translateY 从 -2px 改为 -1px（M3 subtle interaction）
- `.post-card` transition 新增 background-image

### functions.php
- FLAVOR_VERSION: 2.18.0 → 2.19.0

### style.css
- Version header: 2.12.0 → 2.19.0（仅本地，下次打包生效）

## 技术要点
- `background-image: linear-gradient(tint, tint)` 叠加技术：tint 在 background-color 之上，light mode 为 transparent 无副作用
- 动态颜色引擎会将种子色映射为 surface-tint，实际颜色不一定是默认紫色（如本站为 #ffdf99 暖金色）
- `color-mix()` 浏览器兼容性良好（2024+ 主流浏览器均支持）

## 验证结果
- `ver=2.19.0` ✅
- Dark mode tint tokens 正确解析为 color-mix 值 ✅
- Post Card: tint-1 (5%) 已应用 ✅
- Footer: tint-2 (8%) 已应用 ✅
- App Bar: `.scrolled` 状态下 tint-2 生效 ✅
