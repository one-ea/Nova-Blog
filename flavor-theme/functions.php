<?php
// 主题常量
define('FLAVOR_VERSION', '2.6.5');
define('FLAVOR_DIR', get_template_directory());
define('FLAVOR_URI', get_template_directory_uri());

// 加载功能模块
require_once FLAVOR_DIR . '/inc/theme-support.php';
require_once FLAVOR_DIR . '/inc/enqueue.php';
require_once FLAVOR_DIR . '/inc/customizer.php';
require_once FLAVOR_DIR . '/inc/widgets.php';
require_once FLAVOR_DIR . '/inc/seo.php';
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
