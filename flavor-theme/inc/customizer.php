<?php
function flavor_customize_register($wp_customize) {
    // === 颜色与外观 ===
    $wp_customize->add_section('flavor_colors', [
        'title' => __('Colors & Appearance', 'flavor'),
        'priority' => 30,
    ]);

    // 种子色
    $wp_customize->add_setting('flavor_seed_color', [
        'default' => '#6750A4',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'flavor_seed_color', [
        'label' => __('Seed Color', 'flavor'),
        'description' => __('Choose a seed color to generate the entire color scheme.', 'flavor'),
        'section' => 'flavor_colors',
    ]));

    // Dark Mode 默认行为
    $wp_customize->add_setting('flavor_dark_mode', [
        'default' => 'auto',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_dark_mode', [
        'label' => __('Dark Mode', 'flavor'),
        'section' => 'flavor_colors',
        'type' => 'select',
        'choices' => [
            'auto' => __('Follow System', 'flavor'),
            'light' => __('Always Light', 'flavor'),
            'dark' => __('Always Dark', 'flavor'),
        ],
    ]);

    // === 布局 ===
    $wp_customize->add_section('flavor_layout', [
        'title' => __('Layout', 'flavor'),
        'priority' => 35,
    ]);

    // 首页布局
    $wp_customize->add_setting('flavor_home_layout', [
        'default' => 'classic',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_home_layout', [
        'label' => __('Homepage Layout', 'flavor'),
        'section' => 'flavor_layout',
        'type' => 'select',
        'choices' => [
            'classic' => __('Classic Blog', 'flavor'),
            'magazine' => __('Magazine Grid', 'flavor'),
            'minimal' => __('Minimal', 'flavor'),
        ],
    ]);

    // 侧边栏位置
    $wp_customize->add_setting('flavor_sidebar_position', [
        'default' => 'right',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_sidebar_position', [
        'label' => __('Sidebar Position', 'flavor'),
        'section' => 'flavor_layout',
        'type' => 'select',
        'choices' => [
            'right' => __('Right', 'flavor'),
            'left' => __('Left', 'flavor'),
            'none' => __('No Sidebar', 'flavor'),
        ],
    ]);

    // 内容宽度
    $wp_customize->add_setting('flavor_content_width', [
        'default' => 'standard',
        'sanitize_callback' => 'flavor_sanitize_select',
    ]);
    $wp_customize->add_control('flavor_content_width', [
        'label' => __('Content Width', 'flavor'),
        'section' => 'flavor_layout',
        'type' => 'select',
        'choices' => [
            'narrow' => __('Narrow (720px)', 'flavor'),
            'standard' => __('Standard (840px)', 'flavor'),
            'wide' => __('Wide (960px)', 'flavor'),
        ],
    ]);

    // === 文章设置 ===
    $wp_customize->add_section('flavor_post', [
        'title' => __('Post Settings', 'flavor'),
        'priority' => 40,
    ]);

    // 阅读时间
    $wp_customize->add_setting('flavor_show_reading_time', ['default' => true, 'sanitize_callback' => 'flavor_sanitize_checkbox']);
    $wp_customize->add_control('flavor_show_reading_time', [
        'label' => __('Show Reading Time', 'flavor'),
        'section' => 'flavor_post',
        'type' => 'checkbox',
    ]);

    // TOC
    $wp_customize->add_setting('flavor_show_toc', ['default' => true, 'sanitize_callback' => 'flavor_sanitize_checkbox']);
    $wp_customize->add_control('flavor_show_toc', [
        'label' => __('Show Table of Contents', 'flavor'),
        'section' => 'flavor_post',
        'type' => 'checkbox',
    ]);

    // 相关文章数量
    $wp_customize->add_setting('flavor_related_posts_count', ['default' => 3, 'sanitize_callback' => 'absint']);
    $wp_customize->add_control('flavor_related_posts_count', [
        'label' => __('Related Posts Count', 'flavor'),
        'section' => 'flavor_post',
        'type' => 'number',
        'input_attrs' => ['min' => 0, 'max' => 6],
    ]);

    // === 页脚 ===
    $wp_customize->add_section('flavor_footer', [
        'title' => __('Footer', 'flavor'),
        'priority' => 50,
    ]);

    $wp_customize->add_setting('flavor_footer_text', [
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('flavor_footer_text', [
        'label' => __('Footer Text', 'flavor'),
        'section' => 'flavor_footer',
        'type' => 'textarea',
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
