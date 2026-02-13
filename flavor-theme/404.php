<?php
get_header();
?>
<div class="container content-area error-page">
    <?php flavor_breadcrumbs(); ?>

    <h1 class="error-page__code">404</h1>
    <h2 class="text-headline-medium mb-16"><?php esc_html_e('Page not found', 'flavor'); ?></h2>
    <p class="text-body-large text-on-surface-variant mb-32"><?php esc_html_e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'flavor'); ?></p>

    <div class="error-page__search">
        <?php get_search_form(); ?>
    </div>

    <a href="<?php echo esc_url(home_url('/')); ?>" class="md-button-filled md-ripple">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="margin-right:8px"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <?php esc_html_e('Back to Home', 'flavor'); ?>
    </a>
</div>
<?php get_footer(); ?>
