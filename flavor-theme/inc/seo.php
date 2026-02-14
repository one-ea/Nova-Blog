<?php
/**
 * Flavor Theme — 内置 SEO
 * 输出 canonical、Open Graph、Twitter Card、JSON-LD
 * 检测到第三方 SEO 插件时自动让位
 */

// 检测是否有第三方 SEO 插件
function flavor_has_seo_plugin() {
    return defined('WPSEO_VERSION')           // Yoast SEO
        || defined('RANK_MATH_VERSION')       // Rank Math
        || defined('AIOSEO_VERSION')          // All in One SEO
        || class_exists('The_SEO_Framework'); // SEO Framework
}

// 主入口
function flavor_seo_head() {
    if (flavor_has_seo_plugin()) return;

    flavor_seo_canonical();
    flavor_seo_meta_description();
    flavor_seo_open_graph();
    flavor_seo_twitter_card();
    flavor_seo_json_ld();
}
add_action('wp_head', 'flavor_seo_head', 2);

// Canonical URL
function flavor_seo_canonical() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_home() || is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
    } elseif (is_category() || is_tag() || is_tax()) {
        echo '<link rel="canonical" href="' . esc_url(get_term_link(get_queried_object())) . '">' . "\n";
    }
}

// Meta Description
function flavor_seo_meta_description() {
    $desc = '';
    if (is_singular()) {
        $desc = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, '...');
    } elseif (is_home() || is_front_page()) {
        $desc = get_bloginfo('description');
    } elseif (is_category() || is_tag()) {
        $desc = term_description();
    }
    $desc = wp_strip_all_tags(trim($desc));
    if ($desc) {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }
}

// Open Graph
function flavor_seo_open_graph() {
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr(get_locale()) . '">' . "\n";

    if (is_singular()) {
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        $desc = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, '...');
        echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($desc)) . '">' . "\n";
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . "\n";
        if (has_post_thumbnail()) {
            $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($img) {
                echo '<meta property="og:image" content="' . esc_url($img[0]) . '">' . "\n";
                echo '<meta property="og:image:width" content="' . esc_attr($img[1]) . '">' . "\n";
                echo '<meta property="og:image:height" content="' . esc_attr($img[2]) . '">' . "\n";
            }
        }
    } else {
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(wp_get_document_title()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url($_SERVER['REQUEST_URI'])) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(get_bloginfo('description')) . '">' . "\n";
    }
}

// Twitter Card
function flavor_seo_twitter_card() {
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    if (is_singular()) {
        echo '<meta name="twitter:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        $desc = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, '...');
        echo '<meta name="twitter:description" content="' . esc_attr(wp_strip_all_tags($desc)) . '">' . "\n";
        if (has_post_thumbnail()) {
            $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($img) echo '<meta name="twitter:image" content="' . esc_url($img[0]) . '">' . "\n";
        }
    }
}

// JSON-LD 结构化数据
function flavor_seo_json_ld() {
    $schemas = [];

    // WebSite schema（所有页面）
    $schemas[] = [
        '@type' => 'WebSite',
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'description' => get_bloginfo('description'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    // Article schema（文章页）
    if (is_singular('post')) {
        $article = [
            '@type' => 'Article',
            'headline' => get_the_title(),
            'url' => get_permalink(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => [
                '@type' => 'Person',
                'name' => get_the_author(),
                'url' => get_author_posts_url(get_the_author_meta('ID')),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url('/'),
            ],
        ];
        if (has_post_thumbnail()) {
            $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
            if ($img) {
                $article['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $img[0],
                    'width' => $img[1],
                    'height' => $img[2],
                ];
            }
        }
        $desc = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, '...');
        $article['description'] = wp_strip_all_tags($desc);
        $schemas[] = $article;
    }

    // 输出
    $output = [
        '@context' => 'https://schema.org',
        '@graph' => $schemas,
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

// 给 gravatar 头像补 alt 属性
function flavor_fix_avatar_alt($avatar, $id_or_email) {
    if (strpos($avatar, 'alt=""') !== false) {
        $name = '';
        if (is_numeric($id_or_email)) {
            $user = get_user_by('id', $id_or_email);
            if ($user) $name = $user->display_name;
        } elseif (is_object($id_or_email) && isset($id_or_email->comment_author)) {
            $name = $id_or_email->comment_author;
        }
        if ($name) {
            $avatar = str_replace('alt=""', 'alt="' . esc_attr($name) . '"', $avatar);
        }
    }
    return $avatar;
}
add_filter('get_avatar', 'flavor_fix_avatar_alt', 10, 2);
