<?php
// 注册小工具区域
function flavor_widgets_init() {
    // 侧边栏
    register_sidebar([
        'name' => __('侧边栏', 'flavor'),
        'id' => 'sidebar-1',
        'description' => __('在此添加小工具以显示在侧边栏。', 'flavor'),
        'before_widget' => '<div id="%1$s" class="widget md-card-outlined %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title text-title-medium">',
        'after_title' => '</h3>',
    ]);

    // 页脚 3 列
    for ($i = 1; $i <= 3; $i++) {
        register_sidebar([
            'name' => sprintf(__('页脚第 %d 栏', 'flavor'), $i),
            'id' => 'footer-' . $i,
            'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title text-title-medium">',
            'after_title' => '</h3>',
        ]);
    }

    // 文章底部
    register_sidebar([
        'name' => __('文章底部', 'flavor'),
        'id' => 'after-post',
        'description' => __('在文章内容后显示的小工具。', 'flavor'),
        'before_widget' => '<div id="%1$s" class="widget after-post-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title text-title-medium">',
        'after_title' => '</h3>',
    ]);
}
add_action('widgets_init', 'flavor_widgets_init');
