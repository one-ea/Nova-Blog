<?php
// ─── 一次性迁移：从旧主题 slug 复制 Customizer 设置 ──────
// 原因：部署时 WordPress 将主题装入 flavor-theme（新目录），
// 而旧设置在 theme_mods_flavor-theme-1 中，需要迁移过来。
add_action('after_setup_theme', function () {
    if (get_option('flavor_mods_migrated_v1')) return;

    $old_mods = get_option('theme_mods_flavor-theme-1');
    if (!$old_mods || !is_array($old_mods)) {
        // 没有旧数据，标记已完成
        update_option('flavor_mods_migrated_v1', true);
        return;
    }

    // 将旧设置合并到当前主题（不覆盖已有的新设置）
    foreach ($old_mods as $key => $value) {
        if (false === get_theme_mod($key)) {
            set_theme_mod($key, $value);
        }
    }

    update_option('flavor_mods_migrated_v1', true);
}, 1);

// 主题常量
define('FLAVOR_VERSION', '2.18.0');
define('FLAVOR_DIR', get_template_directory());
define('FLAVOR_URI', get_template_directory_uri());

// 加载功能模块
require_once FLAVOR_DIR . '/inc/color-engine.php';
require_once FLAVOR_DIR . '/inc/theme-support.php';
require_once FLAVOR_DIR . '/inc/enqueue.php';
require_once FLAVOR_DIR . '/inc/customizer.php';
require_once FLAVOR_DIR . '/inc/widgets.php';
require_once FLAVOR_DIR . '/inc/seo.php';
require_once FLAVOR_DIR . '/inc/seo-metabox.php';
require_once FLAVOR_DIR . '/inc/block-patterns.php';
require_once FLAVOR_DIR . '/inc/block-styles.php';
require_once FLAVOR_DIR . '/inc/walker-comment.php';

// 注册导航菜单
register_nav_menus([
    'primary' => __('主导航', 'flavor'),
    'footer'  => __('页脚导航', 'flavor'),
    'social'  => __('社交链接', 'flavor'),
]);

// Drawer 菜单 fallback（未注册菜单时显示默认链接）
function flavor_fallback_drawer_menu() {
    $pages = get_pages(['sort_column' => 'menu_order', 'number' => 6]);
    echo '<ul class="nav-drawer__menu">';
    echo '<li class="' . (is_front_page() ? 'current-menu-item' : '') . '">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('首页', 'flavor') . '</a></li>';
    foreach ($pages as $page) {
        $is_current = is_page($page->ID) ? 'current-menu-item' : '';
        echo '<li class="' . $is_current . '"><a href="' . esc_url(get_permalink($page)) . '">' . esc_html($page->post_title) . '</a></li>';
    }
    echo '</ul>';
}

// 文章阅读时间计算
function flavor_reading_time($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $content = get_post_field('post_content', $post_id);
    $word_count = mb_strlen(strip_tags($content), 'UTF-8');
    $minutes = max(1, ceil($word_count / 400));
    return sprintf(__('%d 分钟阅读', 'flavor'), $minutes);
}

// 文章浏览次数
function flavor_get_post_views($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $count = get_post_meta($post_id, 'flavor_post_views', true);
    return $count ? intval($count) : 0;
}

// 浏览次数计数（Cookie 节流，同一访客 24h 内不重复计数）
function flavor_set_post_views() {
    if (!is_single() || is_admin()) return;

    $post_id = get_the_ID();
    $cookie_key = 'flavor_viewed_' . $post_id;

    // 已访问过则跳过
    if (isset($_COOKIE[$cookie_key])) return;

    $count = flavor_get_post_views($post_id);
    update_post_meta($post_id, 'flavor_post_views', $count + 1);

    // 设置 24h Cookie 防重复
    setcookie($cookie_key, '1', time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
}
add_action('template_redirect', 'flavor_set_post_views');

// 摘要长度
function flavor_excerpt_length($length) { return 40; }
add_filter('excerpt_length', 'flavor_excerpt_length');

// 摘要后缀
function flavor_excerpt_more($more) { return '...'; }
add_filter('excerpt_more', 'flavor_excerpt_more');

// 面包屑导航
function flavor_breadcrumbs() {
    if (is_front_page()) return;

    $items = [];
    $position = 1;

    $items[] = [
        'url' => home_url('/'),
        'title' => __('首页', 'flavor'),
        'position' => $position++
    ];

    if (is_single()) {
        $categories = get_the_category();
        if (!empty($categories)) {
            $category = $categories[0];
            $items[] = [
                'url' => get_category_link($category->term_id),
                'title' => $category->name,
                'position' => $position++
            ];
        }
        $items[] = ['url' => '', 'title' => get_the_title(), 'position' => $position++];
    } elseif (is_page()) {
        $items[] = ['url' => '', 'title' => get_the_title(), 'position' => $position++];
    } elseif (is_category()) {
        $items[] = ['url' => '', 'title' => single_cat_title('', false), 'position' => $position++];
    } elseif (is_tag()) {
        $items[] = ['url' => '', 'title' => single_tag_title('', false), 'position' => $position++];
    } elseif (is_author()) {
        $items[] = ['url' => '', 'title' => get_the_author(), 'position' => $position++];
    } elseif (is_date()) {
        if (is_year()) {
            $items[] = ['url' => '', 'title' => get_the_date('Y'), 'position' => $position++];
        } elseif (is_month()) {
            $items[] = ['url' => '', 'title' => get_the_date('F Y'), 'position' => $position++];
        } elseif (is_day()) {
            $items[] = ['url' => '', 'title' => get_the_date(), 'position' => $position++];
        }
    } elseif (is_search()) {
        $items[] = ['url' => '', 'title' => __('搜索结果', 'flavor'), 'position' => $position++];
    } elseif (is_404()) {
        $items[] = ['url' => '', 'title' => '404', 'position' => $position++];
    }

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('面包屑导航', 'flavor') . '">';
    echo '<ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">';

    $total = count($items);
    foreach ($items as $index => $item) {
        $is_last = ($index === $total - 1);
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        if ($is_last || empty($item['url'])) {
            echo '<span class="breadcrumb-item breadcrumb-item--current" itemprop="name">' . esc_html($item['title']) . '</span>';
        } else {
            echo '<a class="breadcrumb-item" itemprop="item" href="' . esc_url($item['url']) . '">';
            echo '<span itemprop="name">' . esc_html($item['title']) . '</span></a>';
        }
        echo '<meta itemprop="position" content="' . $item['position'] . '">';
        echo '</li>';
        if (!$is_last) {
            echo '<li class="breadcrumbs__separator" aria-hidden="true">';
            echo '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>';
            echo '</li>';
        }
    }

    echo '</ol></nav>';
}

// HTTP 安全响应头
function flavor_security_headers() {
    if (is_admin()) return;
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}
add_action('send_headers', 'flavor_security_headers');

// 文章详情页阅读进度条
function flavor_reading_progress_bar() {
    if (!is_single()) return;
    echo '<div class="reading-progress" aria-hidden="true"><div class="reading-progress__bar"></div></div>' . "\n";
}
add_action('wp_body_open', 'flavor_reading_progress_bar');

// 发布/更新/删除文章时清除 footer 缓存
function flavor_clear_footer_cache() {
    delete_transient('flavor_footer_recent');
    delete_transient('flavor_categories_cache');
}
add_action('save_post', 'flavor_clear_footer_cache');
add_action('delete_post', 'flavor_clear_footer_cache');
add_action('created_category', 'flavor_clear_footer_cache');
add_action('edited_category', 'flavor_clear_footer_cache');
add_action('delete_category', 'flavor_clear_footer_cache');

// ─── 个性化：body_class 过滤器 ─────────────────────────
function flavor_personalization_body_class($classes) {
    $map = [
        'flavor_font_family'     => ['system'      => '', 'serif' => 'font-serif', 'rounded' => 'font-rounded'],
        'flavor_font_scale'      => ['standard'    => '', 'compact' => 'scale-compact', 'large' => 'scale-large'],
        'flavor_corner_style'    => ['rounded'     => '', 'sharp' => 'corners-sharp', 'pill' => 'corners-pill'],
        'flavor_card_style'      => ['elevated'    => '', 'outlined' => 'cards-outlined', 'filled' => 'cards-filled'],
        'flavor_content_density' => ['comfortable' => '', 'compact' => 'density-compact', 'spacious' => 'density-spacious'],
    ];
    foreach ($map as $mod => $values) {
        $defaults = ['flavor_font_family' => 'system', 'flavor_font_scale' => 'standard', 'flavor_corner_style' => 'rounded', 'flavor_card_style' => 'elevated', 'flavor_content_density' => 'comfortable'];
        $val = get_theme_mod($mod, $defaults[$mod]);
        if (!empty($values[$val])) {
            $classes[] = $values[$val];
        }
    }
    if (!get_theme_mod('flavor_enable_animations', true)) {
        $classes[] = 'no-animations';
    }
    return $classes;
}
add_filter('body_class', 'flavor_personalization_body_class');

// ─── 个性化：内联 CSS 自定义属性覆盖 ────────────────────
function flavor_personalization_css() {
    $rules = [];

    // 字体系列
    $font = get_theme_mod('flavor_font_family', 'system');
    if ($font === 'serif') {
        $rules[] = ':root{--md-sys-typescale-display-font:"Noto Serif SC",Georgia,serif;--md-sys-typescale-text-font:"Noto Serif SC",Georgia,serif}';
    } elseif ($font === 'rounded') {
        $rules[] = ':root{--md-sys-typescale-display-font:"Nunito","Noto Sans SC",system-ui,sans-serif;--md-sys-typescale-text-font:"Nunito","Noto Sans SC",system-ui,sans-serif}';
    }

    // 字体缩放
    $scale = get_theme_mod('flavor_font_scale', 'standard');
    if ($scale === 'compact') {
        $rules[] = ':root{--md-sys-typescale-display-large-size:50px;--md-sys-typescale-display-large-line-height:56px;--md-sys-typescale-headline-large-size:28px;--md-sys-typescale-headline-large-line-height:34px;--md-sys-typescale-body-large-size:14px;--md-sys-typescale-body-large-line-height:20px}';
    } elseif ($scale === 'large') {
        $rules[] = ':root{--md-sys-typescale-display-large-size:64px;--md-sys-typescale-display-large-line-height:72px;--md-sys-typescale-headline-large-size:36px;--md-sys-typescale-headline-large-line-height:44px;--md-sys-typescale-body-large-size:18px;--md-sys-typescale-body-large-line-height:26px}';
    }

    // 圆角风格
    $corners = get_theme_mod('flavor_corner_style', 'rounded');
    if ($corners === 'sharp') {
        $rules[] = ':root{--md-sys-shape-corner-extra-small:0px;--md-sys-shape-corner-small:0px;--md-sys-shape-corner-medium:0px;--md-sys-shape-corner-large:0px;--md-sys-shape-corner-extra-large:0px}';
    } elseif ($corners === 'pill') {
        $rules[] = ':root{--md-sys-shape-corner-extra-small:8px;--md-sys-shape-corner-small:16px;--md-sys-shape-corner-medium:24px;--md-sys-shape-corner-large:28px;--md-sys-shape-corner-extra-large:9999px}';
    }

    if (empty($rules)) return;
    echo '<style id="flavor-personalization">' . implode('', $rules) . '</style>' . "\n";
}
add_action('wp_head', 'flavor_personalization_css', 3);

// ─── 个性化：卡片类名辅助函数 ──────────────────────────
function flavor_card_class() {
    $style = get_theme_mod('flavor_card_style', 'elevated');
    $map = [
        'elevated' => 'md-card-elevated',
        'outlined' => 'md-card-outlined',
        'filled'   => 'md-card-filled',
    ];
    return $map[$style] ?? 'md-card-elevated';
}

// 首页排除置顶文章（已在 featured 区单独展示）
function flavor_exclude_sticky_from_main($query) {
    if ($query->is_home() && $query->is_main_query() && !$query->is_paged()) {
        // 当 featured 区关闭时，不排除置顶文章
        if (!get_theme_mod('flavor_show_featured', true)) return;
        $sticky = get_option('sticky_posts');
        if (!empty($sticky)) {
            $query->set('ignore_sticky_posts', 1);
            $existing = $query->get('post__not_in') ?: [];
            $query->set('post__not_in', array_merge($existing, $sticky));
        }
    }
}
add_action('pre_get_posts', 'flavor_exclude_sticky_from_main');

// ─── AJAX 点赞 ─────────────────────────────────────────

function flavor_like_post() {
    check_ajax_referer('flavor_post_actions', 'nonce');

    $post_id = absint($_POST['post_id'] ?? 0);
    if (!$post_id || !get_post($post_id)) {
        wp_send_json_error('Invalid post', 400);
    }

    $count = (int) get_post_meta($post_id, 'flavor_post_likes', true);
    $count++;
    update_post_meta($post_id, 'flavor_post_likes', $count);

    wp_send_json_success(['count' => $count]);
}
add_action('wp_ajax_flavor_like_post', 'flavor_like_post');
add_action('wp_ajax_nopriv_flavor_like_post', 'flavor_like_post');

function flavor_get_post_likes($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $count = get_post_meta($post_id, 'flavor_post_likes', true);
    return $count ? intval($count) : 0;
}

// ─── AJAX 评论点赞 ───────────────────────────────────────

function flavor_like_comment() {
    check_ajax_referer('flavor_post_actions', 'nonce');

    $comment_id = absint($_POST['comment_id'] ?? 0);
    if (!$comment_id || !get_comment($comment_id)) {
        wp_send_json_error('Invalid comment', 400);
    }

    $count = (int) get_comment_meta($comment_id, 'flavor_comment_likes', true);
    $count++;
    update_comment_meta($comment_id, 'flavor_comment_likes', $count);

    wp_send_json_success(['count' => $count]);
}
add_action('wp_ajax_flavor_like_comment', 'flavor_like_comment');
add_action('wp_ajax_nopriv_flavor_like_comment', 'flavor_like_comment');

function flavor_get_comment_likes($comment_id) {
    $count = get_comment_meta($comment_id, 'flavor_comment_likes', true);
    return $count ? intval($count) : 0;
}

// ─── PWA 支持 ──────────────────────────────────────────

// 动态生成 manifest.json
function flavor_manifest_endpoint() {
    add_rewrite_rule('^manifest\.json$', 'index.php?flavor_manifest=1', 'top');
}
add_action('init', 'flavor_manifest_endpoint');

function flavor_manifest_query_var($vars) {
    $vars[] = 'flavor_manifest';
    return $vars;
}
add_filter('query_vars', 'flavor_manifest_query_var');

function flavor_manifest_output() {
    if (!get_query_var('flavor_manifest')) return;

    $seed = get_theme_mod('flavor_seed_color', '#6750A4');
    $manifest = [
        'name'             => get_bloginfo('name'),
        'short_name'       => mb_substr(get_bloginfo('name'), 0, 12),
        'description'      => get_bloginfo('description'),
        'start_url'        => home_url('/'),
        'display'          => 'standalone',
        'theme_color'      => $seed,
        'background_color' => '#FFFBFE',
        'icons'            => [],
    ];

    // 使用站点图标（如有）
    $site_icon_id = get_option('site_icon');
    if ($site_icon_id) {
        foreach ([192, 512] as $size) {
            $icon = wp_get_attachment_image_url($site_icon_id, [$size, $size]);
            if ($icon) {
                $manifest['icons'][] = [
                    'src'   => $icon,
                    'sizes' => $size . 'x' . $size,
                    'type'  => 'image/png',
                ];
            }
        }
    }

    header('Content-Type: application/manifest+json; charset=utf-8');
    echo wp_json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}
add_action('template_redirect', 'flavor_manifest_output', 0);

// 注入 <link rel="manifest"> 和 SW 注册脚本
function flavor_pwa_head() {
    echo '<link rel="manifest" href="' . esc_url(home_url('/manifest.json')) . '">' . "\n";
    $seed = get_theme_mod('flavor_seed_color', '#6750A4');
    echo '<meta name="theme-color" content="' . esc_attr($seed) . '">' . "\n";
}
add_action('wp_head', 'flavor_pwa_head', 2);

function flavor_register_sw() {
    $sw_url = home_url('/sw.js');
    echo '<script>if("serviceWorker"in navigator){navigator.serviceWorker.register("' . esc_js($sw_url) . '",{scope:"/"})}</script>' . "\n";
}
add_action('wp_footer', 'flavor_register_sw', 99);

// 从根路径提供 Service Worker
function flavor_sw_rewrite() {
    add_rewrite_rule('^sw\.js$', 'index.php?flavor_sw=1', 'top');
}
add_action('init', 'flavor_sw_rewrite');

function flavor_sw_query_var($vars) {
    $vars[] = 'flavor_sw';
    return $vars;
}
add_filter('query_vars', 'flavor_sw_query_var');

function flavor_sw_output() {
    if (!get_query_var('flavor_sw')) return;

    $sw_file = FLAVOR_DIR . '/sw.js';
    if (!file_exists($sw_file)) return;

    header('Content-Type: application/javascript; charset=utf-8');
    header('Service-Worker-Allowed: /');
    header('Cache-Control: no-cache');
    readfile($sw_file);
    exit;
}
add_action('template_redirect', 'flavor_sw_output', 0);

// 离线 fallback 路由
function flavor_offline_rewrite() {
    add_rewrite_rule('^offline/?$', 'index.php?flavor_offline=1', 'top');
}
add_action('init', 'flavor_offline_rewrite');

function flavor_offline_query_var($vars) {
    $vars[] = 'flavor_offline';
    return $vars;
}
add_filter('query_vars', 'flavor_offline_query_var');

function flavor_offline_template($template) {
    if (get_query_var('flavor_offline')) {
        $offline = FLAVOR_DIR . '/offline.php';
        if (file_exists($offline)) return $offline;
    }
    return $template;
}
add_filter('template_include', 'flavor_offline_template');
