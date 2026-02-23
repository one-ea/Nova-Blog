<?php
function flavor_customize_register($wp_customize) {
    // === 颜色与外观 ===
    $wp_customize->add_section('flavor_colors', [
        'title' => __('颜色与外观', 'flavor'),
        'priority' => 30,
    ]);

    // 种子色
    $wp_customize->add_setting('flavor_seed_color', [
        'default' => '#6750A4',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'flavor_seed_color', [
        'label' => __('种子色', 'flavor'),
        'description' => __('选择一个种子色来生成整套配色方案。', 'flavor'),
        'section' => 'flavor_colors',
    ]));

    // Dark Mode 默认行为
    $wp_customize->add_setting('flavor_dark_mode', [
        'default' => 'auto',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_dark_mode', [
        'label' => __('深色模式', 'flavor'),
        'section' => 'flavor_colors',
        'type' => 'select',
        'choices' => [
            'auto' => __('跟随系统', 'flavor'),
            'light' => __('始终浅色', 'flavor'),
            'dark' => __('始终深色', 'flavor'),
        ],
    ]);

    // === 布局 ===
    $wp_customize->add_section('flavor_layout', [
        'title' => __('布局', 'flavor'),
        'priority' => 35,
    ]);

    // 首页布局
    $wp_customize->add_setting('flavor_home_layout', [
        'default' => 'classic',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_home_layout', [
        'label' => __('首页布局', 'flavor'),
        'section' => 'flavor_layout',
        'type' => 'select',
        'choices' => [
            'classic' => __('经典博客', 'flavor'),
            'magazine' => __('杂志网格', 'flavor'),
            'minimal' => __('极简', 'flavor'),
        ],
    ]);

    // 侧边栏位置
    $wp_customize->add_setting('flavor_sidebar_position', [
        'default' => 'right',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_sidebar_position', [
        'label' => __('侧边栏位置', 'flavor'),
        'section' => 'flavor_layout',
        'type' => 'select',
        'choices' => [
            'right' => __('右侧', 'flavor'),
            'left' => __('左侧', 'flavor'),
            'none' => __('无侧边栏', 'flavor'),
        ],
    ]);

    // 内容宽度
    $wp_customize->add_setting('flavor_content_width', [
        'default' => 'standard',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_content_width', [
        'label' => __('内容宽度', 'flavor'),
        'section' => 'flavor_layout',
        'type' => 'select',
        'choices' => [
            'narrow' => __('窄（720px）', 'flavor'),
            'standard' => __('标准（840px）', 'flavor'),
            'wide' => __('宽（960px）', 'flavor'),
        ],
    ]);

    // === 文章设置 ===
    $wp_customize->add_section('flavor_post', [
        'title' => __('文章设置', 'flavor'),
        'priority' => 40,
    ]);

    // 阅读时间
    $wp_customize->add_setting('flavor_show_reading_time', ['default' => true, 'sanitize_callback' => 'flavor_sanitize_checkbox']);
    $wp_customize->add_control('flavor_show_reading_time', [
        'label' => __('显示阅读时间', 'flavor'),
        'section' => 'flavor_post',
        'type' => 'checkbox',
    ]);

    // TOC
    $wp_customize->add_setting('flavor_show_toc', ['default' => true, 'sanitize_callback' => 'flavor_sanitize_checkbox']);
    $wp_customize->add_control('flavor_show_toc', [
        'label' => __('显示文章目录', 'flavor'),
        'section' => 'flavor_post',
        'type' => 'checkbox',
    ]);

    // 相关文章数量
    $wp_customize->add_setting('flavor_related_posts_count', ['default' => 3, 'sanitize_callback' => 'absint']);
    $wp_customize->add_control('flavor_related_posts_count', [
        'label' => __('相关文章数量', 'flavor'),
        'section' => 'flavor_post',
        'type' => 'number',
        'input_attrs' => ['min' => 0, 'max' => 6],
    ]);

    // === 页脚 ===
    $wp_customize->add_section('flavor_footer', [
        'title' => __('页脚', 'flavor'),
        'priority' => 50,
    ]);

    $wp_customize->add_setting('flavor_footer_text', [
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('flavor_footer_text', [
        'label' => __('页脚文本', 'flavor'),
        'section' => 'flavor_footer',
        'type' => 'textarea',
    ]);

    // === SEO ===
    $wp_customize->add_section('flavor_seo', [
        'title' => __('SEO 设置', 'flavor'),
        'priority' => 42,
    ]);

    // 默认 OG 图片
    $wp_customize->add_setting('flavor_default_og_image', [
        'default' => '',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'flavor_default_og_image', [
        'label' => __('默认社交分享图片', 'flavor'),
        'description' => __('当文章没有特色图片和自定义 OG 图时使用，推荐 1200×630', 'flavor'),
        'section' => 'flavor_seo',
        'mime_type' => 'image',
    ]));

    // === 社交链接 ===
    $wp_customize->add_section('flavor_social', [
        'title' => __('社交链接', 'flavor'),
        'priority' => 45,
    ]);

    $social_links = [
        'github'  => ['label' => 'GitHub', 'default' => ''],
        'twitter' => ['label' => 'Twitter / X', 'default' => ''],
        'email'   => ['label' => __('邮箱地址', 'flavor'), 'default' => ''],
        'rss'     => ['label' => 'RSS', 'default' => ''],
    ];

    foreach ($social_links as $key => $opts) {
        $setting_id = 'flavor_social_' . $key;
        $wp_customize->add_setting($setting_id, [
            'default' => $opts['default'],
            'sanitize_callback' => $key === 'email' ? 'sanitize_email' : 'esc_url_raw',
        ]);
        $wp_customize->add_control($setting_id, [
            'label' => $opts['label'],
            'section' => 'flavor_social',
            'type' => $key === 'email' ? 'email' : 'url',
        ]);
    }

    // === 关于页 ===
    $wp_customize->add_section('flavor_about', [
        'title' => __('关于页', 'flavor'),
        'priority' => 48,
    ]);

    // 兴趣标签（逗号分隔）
    $wp_customize->add_setting('flavor_about_interests', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('flavor_about_interests', [
        'label' => __('兴趣与技能标签', 'flavor'),
        'description' => __('用逗号分隔，如：摄影, 编程, 烘焙, 旅行', 'flavor'),
        'section' => 'flavor_about',
        'type' => 'textarea',
    ]);

    // ═══════════════════════════════════════════════════════════
    // 个性化面板
    // ═══════════════════════════════════════════════════════════
    $wp_customize->add_panel('flavor_personalization', [
        'title'    => __('个性化', 'flavor'),
        'priority' => 32,
    ]);

    // --- 1A. 字体与排版 ---
    $wp_customize->add_section('flavor_typography', [
        'title' => __('字体与排版', 'flavor'),
        'panel' => 'flavor_personalization',
    ]);

    $wp_customize->add_setting('flavor_font_family', [
        'default'           => 'system',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_font_family', [
        'label'   => __('字体系列', 'flavor'),
        'section' => 'flavor_typography',
        'type'    => 'select',
        'choices' => [
            'system'  => __('系统默认', 'flavor'),
            'serif'   => __('衬线体', 'flavor'),
            'rounded' => __('圆体', 'flavor'),
        ],
    ]);

    $wp_customize->add_setting('flavor_font_scale', [
        'default'           => 'standard',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_font_scale', [
        'label'   => __('字体大小', 'flavor'),
        'section' => 'flavor_typography',
        'type'    => 'select',
        'choices' => [
            'compact'  => __('紧凑', 'flavor'),
            'standard' => __('标准', 'flavor'),
            'large'    => __('大号', 'flavor'),
        ],
    ]);

    // --- 1B. 视觉风格 ---
    $wp_customize->add_section('flavor_visual', [
        'title' => __('视觉风格', 'flavor'),
        'panel' => 'flavor_personalization',
    ]);

    $wp_customize->add_setting('flavor_corner_style', [
        'default'           => 'rounded',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_corner_style', [
        'label'   => __('圆角风格', 'flavor'),
        'section' => 'flavor_visual',
        'type'    => 'select',
        'choices' => [
            'rounded' => __('圆角（默认）', 'flavor'),
            'sharp'   => __('直角', 'flavor'),
            'pill'    => __('胶囊', 'flavor'),
        ],
    ]);

    $wp_customize->add_setting('flavor_card_style', [
        'default'           => 'elevated',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_card_style', [
        'label'   => __('卡片风格', 'flavor'),
        'section' => 'flavor_visual',
        'type'    => 'select',
        'choices' => [
            'elevated' => __('浮起（默认）', 'flavor'),
            'outlined' => __('描边', 'flavor'),
            'filled'   => __('填充', 'flavor'),
        ],
    ]);

    $wp_customize->add_setting('flavor_content_density', [
        'default'           => 'comfortable',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_content_density', [
        'label'   => __('内容密度', 'flavor'),
        'section' => 'flavor_visual',
        'type'    => 'select',
        'choices' => [
            'comfortable' => __('舒适（默认）', 'flavor'),
            'compact'     => __('紧凑', 'flavor'),
            'spacious'    => __('宽松', 'flavor'),
        ],
    ]);

    // --- 1C. 首页模块 ---
    $wp_customize->add_section('flavor_homepage_modules', [
        'title' => __('首页模块', 'flavor'),
        'panel' => 'flavor_personalization',
    ]);

    $wp_customize->add_setting('flavor_show_hero', [
        'default'           => true,
        'sanitize_callback' => 'flavor_sanitize_checkbox',
    ]);
    $wp_customize->add_control('flavor_show_hero', [
        'label'   => __('显示 Hero 区域', 'flavor'),
        'section' => 'flavor_homepage_modules',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('flavor_show_featured', [
        'default'           => true,
        'sanitize_callback' => 'flavor_sanitize_checkbox',
    ]);
    $wp_customize->add_control('flavor_show_featured', [
        'label'   => __('显示置顶精选文章', 'flavor'),
        'section' => 'flavor_homepage_modules',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('flavor_show_category_filter', [
        'default'           => true,
        'sanitize_callback' => 'flavor_sanitize_checkbox',
    ]);
    $wp_customize->add_control('flavor_show_category_filter', [
        'label'   => __('显示分类筛选标签', 'flavor'),
        'section' => 'flavor_homepage_modules',
        'type'    => 'checkbox',
    ]);

    // --- 1D. 动画与交互 ---
    $wp_customize->add_section('flavor_animations', [
        'title' => __('动画与交互', 'flavor'),
        'panel' => 'flavor_personalization',
    ]);

    $wp_customize->add_setting('flavor_enable_animations', [
        'default'           => true,
        'sanitize_callback' => 'flavor_sanitize_checkbox',
    ]);
    $wp_customize->add_control('flavor_enable_animations', [
        'label'   => __('启用动画效果', 'flavor'),
        'section' => 'flavor_animations',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('flavor_enable_scroll_animations', [
        'default'           => true,
        'sanitize_callback' => 'flavor_sanitize_checkbox',
    ]);
    $wp_customize->add_control('flavor_enable_scroll_animations', [
        'label'   => __('启用滚动动画', 'flavor'),
        'section' => 'flavor_animations',
        'type'    => 'checkbox',
    ]);
}
add_action('customize_register', 'flavor_customize_register');

// Sanitize helpers
function flavor_sanitize_select($input, $setting) {
    $choices = $setting->manager->get_control($setting->id)->choices;
    return array_key_exists($input, $choices) ? $input : $setting->default;
}

function flavor_sanitize_checkbox($input) {
    return (bool) $input;
}

// Customizer 实时预览脚本
function flavor_customize_preview_js() {
    wp_enqueue_script('flavor-customizer-preview', FLAVOR_URI . '/assets/js/customizer-preview.js', ['customize-preview', 'flavor-color-engine'], FLAVOR_VERSION, true);
}
add_action('customize_preview_init', 'flavor_customize_preview_js');
