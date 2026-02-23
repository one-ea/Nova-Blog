# 评论区与正文对齐修复

**日期**: 2026-02-23

## 问题

文章详情页（single.php）中，正文区域 `.post-content` 在 1080px 的 `.container` flex 布局里靠左排列（右侧是 TOC 目录），而底部区域（标签、操作按钮、作者卡片、相关文章、评论区）使用 `.container.container--narrow`（720px + `margin: 0 auto`），导致底部内容居中，与靠左的正文区域水平不对齐。

## 修复方案

### `single.php`
- 将底部内容从独立的 `<div class="container container--narrow">` 中移出
- 放入与正文相同的 `<div class="container">` 内，用 `<div class="post-bottom">` 包裹
- 结构：`container > post-content-wrapper + post-bottom`（并列关系）

### `assets/css/theme.css`
- 新增 `.post-bottom`：`max-width: 720px; padding-bottom: 32px;`
- 不设 `margin: 0 auto` → 在 1080px container 内自然靠左，与 `.post-content` 对齐

## 根因
- `.post-content`：flex 容器中 `flex: 1; max-width: 720px` → 靠左
- `.container--narrow`：`max-width: 720px; margin: 0 auto` → 居中
- 两个 720px 区域水平起点不同
