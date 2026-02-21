<?php
get_header();
?>
<div class="container content-area error-page">
    <?php flavor_breadcrumbs(); ?>

    <h1 class="error-page__code">404</h1>
    <h2 class="text-headline-medium mb-16"><?php esc_html_e('页面未找到', 'flavor'); ?></h2>
    <p class="text-body-large text-on-surface-variant mb-32"><?php esc_html_e('您访问的页面可能已被删除、更名或暂时不可用。', 'flavor'); ?></p>

    <div class="error-page__search">
        <?php get_search_form(); ?>
    </div>

    <a href="<?php echo esc_url(home_url('/')); ?>" class="md-button-filled md-ripple">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="margin-right:8px"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <?php esc_html_e('返回首页', 'flavor'); ?>
    </a>
</div>
<?php get_footer(); ?>
