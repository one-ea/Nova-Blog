<?php
/**
 * Flavor Theme - Block Styles
 * Register custom block styles for Gutenberg editor
 */
function flavor_register_block_styles() {
    // Quote styles
    register_block_style( 'core/quote', [
        'name'  => 'flavor-highlight',
        'label' => __( 'Highlight Quote', 'flavor' ),
    ] );

    // Button styles
    register_block_style( 'core/button', [
        'name'  => 'flavor-filled',
        'label' => __( 'Filled', 'flavor' ),
    ] );
    register_block_style( 'core/button', [
        'name'  => 'flavor-tonal',
        'label' => __( 'Tonal', 'flavor' ),
    ] );
    register_block_style( 'core/button', [
        'name'  => 'flavor-outlined',
        'label' => __( 'Outlined', 'flavor' ),
    ] );

    // Group styles
    register_block_style( 'core/group', [
        'name'  => 'flavor-card',
        'label' => __( 'Card', 'flavor' ),
    ] );
    register_block_style( 'core/group', [
        'name'  => 'flavor-surface',
        'label' => __( 'Surface Container', 'flavor' ),
    ] );

    // Image styles
    register_block_style( 'core/image', [
        'name'  => 'flavor-rounded',
        'label' => __( 'Rounded', 'flavor' ),
    ] );
}
add_action( 'init', 'flavor_register_block_styles' );
