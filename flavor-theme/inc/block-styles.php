<?php
/**
 * Flavor Theme - Block Styles
 * Register custom block styles for Gutenberg editor
 */
function flavor_register_block_styles() {
    // Quote styles
    register_block_style( 'core/quote', [
        'name'  => 'flavor-highlight',
        'label' => __( '高亮引用', 'flavor' ),
    ] );

    // Button styles
    register_block_style( 'core/button', [
        'name'  => 'flavor-filled',
        'label' => __( '填充', 'flavor' ),
    ] );
    register_block_style( 'core/button', [
        'name'  => 'flavor-tonal',
        'label' => __( '色调', 'flavor' ),
    ] );
    register_block_style( 'core/button', [
        'name'  => 'flavor-outlined',
        'label' => __( '描边', 'flavor' ),
    ] );

    // Group styles
    register_block_style( 'core/group', [
        'name'  => 'flavor-card',
        'label' => __( '卡片', 'flavor' ),
    ] );
    register_block_style( 'core/group', [
        'name'  => 'flavor-surface',
        'label' => __( '表面容器', 'flavor' ),
    ] );

    // Image styles
    register_block_style( 'core/image', [
        'name'  => 'flavor-rounded',
        'label' => __( '圆角', 'flavor' ),
    ] );
}
add_action( 'init', 'flavor_register_block_styles' );
