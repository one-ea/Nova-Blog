<?php
function flavor_register_block_patterns() {
    register_block_pattern_category('flavor', [
        'label' => __('Flavor Theme', 'flavor'),
    ]);

    // Hero Pattern
    register_block_pattern('flavor/hero', [
        'title' => __('Hero Section', 'flavor'),
        'categories' => ['flavor'],
        'content' => '<!-- wp:group {"className":"flavor-hero","style":{"spacing":{"padding":{"top":"80px","bottom":"80px"}}}} -->
<div class="wp-block-group flavor-hero" style="padding-top:80px;padding-bottom:80px">
<!-- wp:heading {"level":1,"className":"text-display-large"} -->
<h1 class="text-display-large">Welcome to My Blog</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"text-body-large"} -->
<p class="text-body-large">Discover stories, ideas, and insights.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"md-button-filled"} -->
<div class="wp-block-button md-button-filled"><a class="wp-block-button__link">Start Reading</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->',
    ]);

    // Card Grid Pattern
    register_block_pattern('flavor/card-grid', [
        'title' => __('Card Grid', 'flavor'),
        'categories' => ['flavor'],
        'content' => '<!-- wp:columns {"className":"grid grid--3"} -->
<div class="wp-block-columns grid grid--3">
<!-- wp:column {"className":"md-card-elevated"} -->
<div class="wp-block-column md-card-elevated">
<!-- wp:heading {"level":3} --><h3>Card Title</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Card description goes here.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"className":"md-card-elevated"} -->
<div class="wp-block-column md-card-elevated">
<!-- wp:heading {"level":3} --><h3>Card Title</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Card description goes here.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"className":"md-card-elevated"} -->
<div class="wp-block-column md-card-elevated">
<!-- wp:heading {"level":3} --><h3>Card Title</h3><!-- /wp:heading -->
<!-- wp:paragraph --><p>Card description goes here.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->',
    ]);
}
add_action('init', 'flavor_register_block_patterns');
