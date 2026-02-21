<?php
/**
 * Flavor Theme — 内置 SEO (v2.3)
 * 输出 canonical、robots、Open Graph、Twitter Card、JSON-LD
 * 检测到第三方 SEO 插件时自动让位
 */

// 检测是否有第三方 SEO 插件
function flavor_has_seo_plugin() {
    return defined('WPSEO_VERSION')           // Yoast SEO
        || defined('RANK_MATH_VERSION')       // Rank Math
        || defined('AIOSEO_VERSION')          // All in One SEO
        || class_exists('The_SEO_Framework'); // SEO Framework
}

// ─── 公共辅助函数 ───────────────────────────────────────

/**
 * 获取当前页面的 SEO 描述文本
 * 统一提取逻辑，消除各模块间的重复代码
 *
 * @param int $word_count 截取词数（默认 50）
 * @return string
 */
function flavor_get_seo_description($word_count = 50) {
    $desc = '';
    if (is_singular()) {
        $desc = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), $word_count, '...');
    } elseif (is_home() || is_front_page()) {
        $desc = get_bloginfo('description');
    } elseif (is_category() || is_tag()) {
        $desc = term_description();
    } elseif (is_author()) {
        $desc = get_the_author_meta('description');
    }
    return wp_strip_all_tags(trim($desc));
}

/**
 * 获取当前页面的 SEO 图片（特色图优先，fallback 到站点 logo）
 *
 * @param string $size 图片尺寸
 * @return array|null ['url' => string, 'width' => int, 'height' => int]
 */
function flavor_get_seo_image($size = 'large') {
    // 文章特色图
    if (is_singular() && has_post_thumbnail()) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id(), $size);
        if ($img) {
            return ['url' => $img[0], 'width' => $img[1], 'height' => $img[2]];
        }
    }
    // Fallback: 自定义 logo
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $img = wp_get_attachment_image_src($logo_id, 'full');
        if ($img) {
            return ['url' => $img[0], 'width' => $img[1], 'height' => $img[2]];
        }
    }
    return null;
}

// ─── 主入口 ─────────────────────────────────────────────

function flavor_seo_head() {
    if (flavor_has_seo_plugin()) return;

    flavor_seo_canonical();
    flavor_seo_robots();
    flavor_seo_meta_description();
    flavor_seo_open_graph();
    flavor_seo_twitter_card();
    flavor_seo_json_ld();
}
add_action('wp_head', 'flavor_seo_head', 2);

// ─── Canonical URL ──────────────────────────────────────

function flavor_seo_canonical() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_home() || is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
    } elseif (is_category() || is_tag() || is_tax()) {
        $url = get_term_link(get_queried_object());
        if (!is_wp_error($url)) {
            echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
        }
    } elseif (is_author()) {
        echo '<link rel="canonical" href="' . esc_url(get_author_posts_url(get_queried_object_id())) . '">' . "\n";
    }

    // 分页 prev/next
    if (is_paged()) {
        global $wp_query;
        $paged = get_query_var('paged', 1);
        if ($paged > 1) {
            echo '<link rel="prev" href="' . esc_url(get_pagenum_link($paged - 1)) . '">' . "\n";
        }
        if ($paged < $wp_query->max_num_pages) {
            echo '<link rel="next" href="' . esc_url(get_pagenum_link($paged + 1)) . '">' . "\n";
        }
    }
}

// ─── Robots Meta ────────────────────────────────────────

function flavor_seo_robots() {
    $robots = [];

    // 分页第 2 页及以后不索引，避免重复内容
    if (is_paged()) {
        $robots[] = 'noindex';
        $robots[] = 'follow';
    }
    // 搜索结果页不索引
    if (is_search()) {
        $robots[] = 'noindex';
        $robots[] = 'follow';
    }
    // 标签归档可选不索引（标签页通常低质量）
    if (is_tag() && apply_filters('flavor_noindex_tags', true)) {
        $robots[] = 'noindex';
        $robots[] = 'follow';
    }

    if (!empty($robots)) {
        echo '<meta name="robots" content="' . esc_attr(implode(', ', $robots)) . '">' . "\n";
    }
}

// ─── Meta Description ───────────────────────────────────

function flavor_seo_meta_description() {
    $desc = flavor_get_seo_description(50);
    if ($desc) {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }
}

// ─── Open Graph ─────────────────────────────────────────

function flavor_seo_open_graph() {
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr(get_locale()) . '">' . "\n";

    if (is_singular()) {
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(flavor_get_seo_description()) . '">' . "\n";
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . "\n";

        // 分类标签
        $categories = get_the_category();
        if ($categories) {
            echo '<meta property="article:section" content="' . esc_attr($categories[0]->name) . '">' . "\n";
        }
        $tags = get_the_tags();
        if ($tags) {
            foreach (array_slice($tags, 0, 5) as $tag) {
                echo '<meta property="article:tag" content="' . esc_attr($tag->name) . '">' . "\n";
            }
        }
    } else {
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(wp_get_document_title()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url(esc_url_raw($_SERVER['REQUEST_URI'] ?? '/'))) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(flavor_get_seo_description()) . '">' . "\n";
    }

    // 所有页面都输出 og:image
    $img = flavor_get_seo_image();
    if ($img) {
        echo '<meta property="og:image" content="' . esc_url($img['url']) . '">' . "\n";
        echo '<meta property="og:image:width" content="' . esc_attr($img['width']) . '">' . "\n";
        echo '<meta property="og:image:height" content="' . esc_attr($img['height']) . '">' . "\n";
    }
}

// ─── Twitter Card ───────────────────────────────────────

function flavor_seo_twitter_card() {
    $img = flavor_get_seo_image();
    echo '<meta name="twitter:card" content="' . ($img ? 'summary_large_image' : 'summary') . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr(is_singular() ? get_the_title() : wp_get_document_title()) . '">' . "\n";

    $desc = flavor_get_seo_description();
    if ($desc) {
        echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    }
    if ($img) {
        echo '<meta name="twitter:image" content="' . esc_url($img['url']) . '">' . "\n";
    }
}

// ─── JSON-LD 结构化数据 ─────────────────────────────────

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

    // 文章页 — 使用 BlogPosting（比 Article 更具体）
    if (is_singular('post')) {
        $article = [
            '@type' => 'BlogPosting',
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
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => get_permalink(),
            ],
            'wordCount' => str_word_count(wp_strip_all_tags(get_the_content())),
        ];

        $img = flavor_get_seo_image('full');
        if ($img) {
            $article['image'] = [
                '@type' => 'ImageObject',
                'url' => $img['url'],
                'width' => $img['width'],
                'height' => $img['height'],
            ];
        }

        // Publisher logo
        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $logo = wp_get_attachment_image_src($logo_id, 'full');
            if ($logo) {
                $article['publisher']['logo'] = [
                    '@type' => 'ImageObject',
                    'url' => $logo[0],
                ];
            }
        }

        $article['description'] = flavor_get_seo_description();

        // 分类
        $categories = get_the_category();
        if ($categories) {
            $article['articleSection'] = $categories[0]->name;
        }

        $schemas[] = $article;
    }

    // 作者归档页 — Person schema
    if (is_author()) {
        $author = get_queried_object();
        if ($author) {
            $schemas[] = [
                '@type' => 'Person',
                'name' => $author->display_name,
                'url' => get_author_posts_url($author->ID),
                'description' => $author->description ?: null,
            ];
        }
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
