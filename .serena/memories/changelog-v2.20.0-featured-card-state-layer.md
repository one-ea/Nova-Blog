# v2.20.0 — Featured Card M3 Token + State Layer System

**Date**: 2026-02-27
**Scope**: M3 Design Audit TOP1 + TOP2

## TOP1: Featured Card Hardcoded Colors → M3 Token

### Problem
Featured Card 使用了 7 处硬编码颜色（`#fff`, `rgba(0,0,0,...)`, `rgba(255,255,255,...)`），不遵循 M3 token 体系。

### Solution
- 新增 `--md-sys-color-on-scrim: #FFFFFF`（不随深色模式改变，因为 scrim 覆盖层上的文字永远是白色）
- 将 `#fff` 替换为 `var(--md-sys-color-on-scrim)`
- 将 `rgba(0,0,0,X)` 替换为 `color-mix(in srgb, var(--md-sys-color-scrim) X%, transparent)`
- 将 `rgba(255,255,255,X)` 替换为 `color-mix(in srgb, var(--md-sys-color-on-scrim) X%, transparent)`

### Files Modified
- `tokens.css`: 新增 `--md-sys-color-on-scrim` token
- `theme.css`: 7 处颜色替换

### Why not `inverse-on-surface`?
`inverse-on-surface` 在 light mode 是 `#F5EFF7`（淡紫），dark mode 是 `#322F35`（深灰），都不是纯白。Featured Card 的 scrim overlay 始终是深色，需要纯白文字。

## TOP2: State Layer Interactive System

### Problem
Post Card 和 Pagination 缺少 M3 State Layer 交互反馈。

### Solution — Post Card
- 添加 `position: relative` 到 `.post-card`
- `::before` 伪元素：`background-color: var(--md-sys-color-on-surface)`, `opacity: 0`, `z-index: 1`
- `:hover::before` → `opacity: 0.08`（hover state layer）
- `:focus-within::before` → `opacity: 0.12`（focus state layer）
- `:active::before` → `opacity: 0.12`（pressed state layer）

### Solution — Pagination
- `::before` state layer on `.pagination .page-numbers a`
- 替换旧的 `background-color` hover 为 state layer opacity
- 使用 `focus-visible` 而非 `focus-within`（pagination links 是直接可聚焦的）

### Design Decision
- Post Card 使用 `focus-within`（因为可聚焦元素是内部 `<a>` 链接）
- Pagination 使用 `focus-visible`（链接本身直接可聚焦）
- `components.css` 已有 chip 和 button 的 state layer，无需重复

### Token Usage
- `--md-sys-state-hover-state-layer-opacity: 0.08`
- `--md-sys-state-focus-state-layer-opacity: 0.12`
- `--md-sys-state-pressed-state-layer-opacity: 0.12`
- `--md-sys-motion-duration-short2: 100ms`
- `--md-sys-motion-easing-standard`
