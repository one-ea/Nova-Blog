<?php
function flavor_theme_support() {
    // 文章特色图片
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(1200, 630, true);
    add_image_size('flavor-card', 600, 400, true);
    add_image_size('flavor-card-small', 300, 200, true);

    // 标题标签
    add_theme_support('title-tag');

    // HTML5 支持
    add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script']);

    // 自定义 Logo
    add_theme_support('custom-logo', [
        'height' => 48,
        'width' => 48,
        'flex-height' => true,
        'flex-width' => true,
    ]);

    // 自定义背景
    add_theme_support('custom-background', [
        'default-color' => 'FEF7FF',
    ]);

    // 编辑器样式
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');

    // Block editor
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');

    // 自动 feed links
    add_theme_support('automatic-feed-links');

    // 内容宽度
    global $content_width;
    if (!isset($content_width)) $content_width = 840;
}
add_action('after_setup_theme', 'flavor_theme_support');
