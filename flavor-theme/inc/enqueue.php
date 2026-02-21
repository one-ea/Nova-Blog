<?php
// 资源路径：SCRIPT_DEBUG 时用源文件，否则用 dist 压缩版
function flavor_asset_uri($subpath) {
    $use_dist = !defined('SCRIPT_DEBUG') || !SCRIPT_DEBUG;
    $dist_path = FLAVOR_DIR . '/assets/dist/' . $subpath;
    if ($use_dist && file_exists($dist_path)) {
        return FLAVOR_URI . '/assets/dist/' . $subpath;
    }
    return FLAVOR_URI . '/assets/' . $subpath;
}

function flavor_enqueue_scripts() {
    // CSS: tokens(内联) → base / components / theme 并行加载 → style.css
    // tokens.css 已内联到 <head>，无需加载
    wp_enqueue_style('flavor-base', flavor_asset_uri('css/base.css'), [], FLAVOR_VERSION);
    wp_enqueue_style('flavor-components', flavor_asset_uri('css/components.css'), [], FLAVOR_VERSION);
    wp_enqueue_style('flavor-theme', flavor_asset_uri('css/theme.css'), [], FLAVOR_VERSION);
    wp_enqueue_style('flavor-style', get_stylesheet_uri(), ['flavor-base', 'flavor-components', 'flavor-theme'], FLAVOR_VERSION);

    // JavaScript（全部 defer）
    wp_enqueue_script('flavor-color-engine', flavor_asset_uri('js/color-engine.js'), [], FLAVOR_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
    wp_enqueue_script('flavor-theme-toggle', flavor_asset_uri('js/theme-toggle.js'), [], FLAVOR_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
    wp_enqueue_script('flavor-ripple', flavor_asset_uri('js/ripple.js'), [], FLAVOR_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
    wp_enqueue_script('flavor-navigation', flavor_asset_uri('js/navigation.js'), [], FLAVOR_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
    wp_enqueue_script('flavor-search', flavor_asset_uri('js/search.js'), [], FLAVOR_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
    wp_enqueue_script('flavor-scroll-enhance', flavor_asset_uri('js/scroll-enhance.js'), [], FLAVOR_VERSION, ['strategy' => 'defer', 'in_footer' => true]);

    // 种子色配置
    wp_localize_script('flavor-color-engine', 'flavorColorConfig', [
        'seedColor' => get_theme_mod('flavor_seed_color', '#6750A4'),
    ]);

    // 文章页加载 TOC
    if (is_single()) {
        wp_enqueue_script('flavor-toc', flavor_asset_uri('js/toc.js'), [], FLAVOR_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
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

// 预加载自托管字体 + 异步加载 Noto Sans SC（中文增强）
function flavor_preload_assets() {
    // DNS 预解析外部资源
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
    // 预加载 Roboto（自托管）
    echo '<link rel="preload" href="' . FLAVOR_URI . '/assets/fonts/roboto-latin.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
    // 异步加载 Noto Sans SC（中文字体太大，不自托管）
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap" media="print" onload="this.media=\'all\'">' . "\n";
    echo '<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap"></noscript>' . "\n";
}
add_action('wp_head', 'flavor_preload_assets', 1);

// 主题色初始化（防闪烁）+ 关键 CSS 内联
function flavor_inline_critical() {
    // 内联 tokens.css（纯 CSS 变量，省一个 HTTP 请求加速 FCP）
    $use_dist = !defined('SCRIPT_DEBUG') || !SCRIPT_DEBUG;
    $tokens_dist = FLAVOR_DIR . '/assets/dist/css/tokens.css';
    $tokens_file = ($use_dist && file_exists($tokens_dist)) ? $tokens_dist : FLAVOR_DIR . '/assets/css/tokens.css';
    if (file_exists($tokens_file)) {
        echo '<style id="flavor-tokens-inline">' . "\n";
        echo file_get_contents($tokens_file);
        echo '</style>' . "\n";
    }
    echo '<script>
    {
        const t = localStorage.getItem("flavor-theme") || "auto";
        const effective = t === "auto"
            ? (window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light")
            : t;
        document.documentElement.setAttribute("data-theme", effective);
    }
    </script>' . "\n";
}
add_action('wp_head', 'flavor_inline_critical', 0);
