<?php
// 主题常量
define('FLAVOR_VERSION', '2.0.0');
define('FLAVOR_DIR', get_template_directory());
define('FLAVOR_URI', get_template_directory_uri());

// 加载功能模块
require_once FLAVOR_DIR . '/inc/theme-support.php';
require_once FLAVOR_DIR . '/inc/enqueue.php';
require_once FLAVOR_DIR . '/inc/customizer.php';
require_once FLAVOR_DIR . '/inc/widgets.php';
require_once FLAVOR_DIR . '/inc/block-patterns.php';
require_once FLAVOR_DIR . '/inc/block-styles.php';
require_once FLAVOR_DIR . '/inc/walker-comment.php';

// 注册导航菜单
register_nav_menus([
    'primary' => __('Primary Navigation', 'flavor'),
    'footer'  => __('Footer Navigation', 'flavor'),
    'social'  => __('Social Links', 'flavor'),
]);

// Drawer 菜单 fallback（未注册菜单时显示默认链接）
function flavor_fallback_drawer_menu() {
    $pages = get_pages(['sort_column' => 'menu_order', 'number' => 6]);
    echo '<ul class="nav-drawer__menu">';
    echo '<li class="' . (is_front_page() ? 'current-menu-item' : '') . '">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'flavor') . '</a></li>';
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
    return sprintf(__('%d min read', 'flavor'), $minutes);
}

// 文章浏览次数
function flavor_get_post_views($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $count = get_post_meta($post_id, 'flavor_post_views', true);
    return $count ? intval($count) : 0;
}

function flavor_set_post_views() {
    if (is_single()) {
        $post_id = get_the_ID();
        $count = flavor_get_post_views($post_id);
        update_post_meta($post_id, 'flavor_post_views', $count + 1);
    }
}
add_action('wp_head', 'flavor_set_post_views');

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
        'title' => __('Home', 'flavor'),
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
        $items[] = ['url' => '', 'title' => __('Search Results', 'flavor'), 'position' => $position++];
    } elseif (is_404()) {
        $items[] = ['url' => '', 'title' => '404', 'position' => $position++];
    }

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'flavor') . '">';
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
