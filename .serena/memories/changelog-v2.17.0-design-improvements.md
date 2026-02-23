# v2.17.0 — TOP1-10 设计改进

## 概述
基于 UI 设计审计（7.5/10 评分），实施了 8 项高优先级设计改进。

## 修改文件

### `assets/css/tokens.css`
- **TOP9**: 修复 Display 字体栈，`"Google Sans"` → `"Playfair Display", Georgia`（L70）
- **TOP3**: 新增 4px 网格间距令牌系统 `--md-sys-spacing-*`（xs/sm/md/lg/xl/2xl/3xl）+ 语义别名（section/card-padding/card-gap/content-gap）
- **TOP4**: 新增 `clamp()` 流式排版覆盖，涵盖 display/headline/title/body 级别的 size 和 line-height
- **TOP6**: 暗色模式令牌紧凑格式化（保持 `[data-theme="dark"]` + `@media prefers-color-scheme` 的安全模式，不做合并）

### `assets/css/base.css`
- **TOP5**: body `line-height` 从 1.75 降至 1.65；`.entry-content` 长文阅读 `line-height: 1.8`
- **TOP5**: `.entry-content h2/h3` 新增 `border-left: 3px solid primary` + `padding-left: 16px` 装饰线
- **TOP5**: `.entry-content h2` 新增 `margin-top: 2.5rem`, `padding-top: 1.5rem`, `::before` 分节线
- **TOP5**: `th` font-weight 从 700 降至 600

### `assets/css/theme.css`
- **TOP1**: `.hero-section` padding `56px→40px`, margin-bottom `48px→32px`; `.featured-post` margin-bottom `40px→28px`
- **TOP2**: `.post-card__placeholder` 渐变使用 `color-mix()` 混入 `surface-container-highest` 提亮暗色模式
- **TOP5**: `.post-title` 从 `display-small-size` 降至 `headline-large-size`, font-weight `700→800`
- **TOP7**: `.footer-default` 新增 `border-top`, section-title 加装饰下划线, 列表项间添加底边框, 链接 hover 加 `translateX(4px)` 位移动效
- **TOP8**: `@container (max-width: 320px)` 隐藏 `.post-card__footer-bottom` 次要信息

### `functions.php`
- `FLAVOR_VERSION`: `2.16.1` → `2.17.0`

## 跳过项
- **TOP10**: @layer 级联管理 — 风险评估过高，现有 specificity 管理良好

## 设计审计关键发现
- 首屏信息密度不足，Hero 占空间过大
- 暗色模式占位图视觉偏暗沉
- Footer 区域缺乏视觉层次
- 文章标题 display-small 在移动端过于夸张
- 字体栈引用了不存在的 Google Sans
