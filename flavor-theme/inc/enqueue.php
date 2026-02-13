<?php
function flavor_enqueue_scripts() {
    // Google Fonts
    wp_enqueue_style('flavor-google-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap', [], null);

    // CSS: tokens → base → components → theme → style.css
    wp_enqueue_style('flavor-tokens', FLAVOR_URI . '/assets/css/tokens.css', [], FLAVOR_VERSION);
    wp_enqueue_style('flavor-base', FLAVOR_URI . '/assets/css/base.css', ['flavor-tokens'], FLAVOR_VERSION);
    wp_enqueue_style('flavor-components', FLAVOR_URI . '/assets/css/components.css', ['flavor-tokens'], FLAVOR_VERSION);
    wp_enqueue_style('flavor-theme', FLAVOR_URI . '/assets/css/theme.css', ['flavor-base', 'flavor-components'], FLAVOR_VERSION);
    wp_enqueue_style('flavor-style', get_stylesheet_uri(), ['flavor-theme'], FLAVOR_VERSION);

    // JavaScript
    wp_enqueue_script('flavor-color-engine', FLAVOR_URI . '/assets/js/color-engine.js', [], FLAVOR_VERSION, true);
    wp_enqueue_script('flavor-theme-toggle', FLAVOR_URI . '/assets/js/theme-toggle.js', [], FLAVOR_VERSION, true);
    wp_enqueue_script('flavor-ripple', FLAVOR_URI . '/assets/js/ripple.js', [], FLAVOR_VERSION, true);
    wp_enqueue_script('flavor-navigation', FLAVOR_URI . '/assets/js/navigation.js', [], FLAVOR_VERSION, true);
    wp_enqueue_script('flavor-search', FLAVOR_URI . '/assets/js/search.js', [], FLAVOR_VERSION, true);

    // 种子色配置
    wp_localize_script('flavor-color-engine', 'flavorColorConfig', [
        'seedColor' => get_theme_mod('flavor_seed_color', '#6750A4'),
    ]);

    // 文章页加载 TOC
    if (is_single()) {
        wp_enqueue_script('flavor-toc', FLAVOR_URI . '/assets/js/toc.js', [], FLAVOR_VERSION, true);
    }

    // 搜索数据
    wp_localize_script('flavor-search', 'flavorData', [
        'restUrl' => esc_url_raw(rest_url()),
        'nonce'   => wp_create_nonce('wp_rest'),
        'homeUrl' => home_url('/'),
    ]);

    // 评论回复
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'flavor_enqueue_scripts');

// 预加载字体
function flavor_preload_assets() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'flavor_preload_assets', 1);

// 主题色初始化（防闪烁）
function flavor_inline_critical_css() {
    echo '<script>
    (function(){
        var t = localStorage.getItem("flavor-theme") || "auto";
        document.documentElement.setAttribute("data-theme", t);
    })();
    </script>' . "\n";
}
add_action('wp_head', 'flavor_inline_critical_css', 0);
