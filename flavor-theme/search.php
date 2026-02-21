<?php
get_header();
?>
<div class="container content-area">
    <?php flavor_breadcrumbs(); ?>

    <header class="search-header mb-32">
        <h1 class="text-headline-large">
            <?php printf(esc_html__('搜索结果：%s', 'flavor'), '<span class="text-primary">' . esc_html(get_search_query()) . '</span>'); ?>
        </h1>
        <p class="text-body-medium text-on-surface-variant mt-8">
            <?php
            global $wp_query;
            printf(esc_html(_n('找到 %d 个结果', '找到 %d 个结果', $wp_query->found_posts, 'flavor')), $wp_query->found_posts);
            ?>
        </p>
    </header>

    <?php if (have_posts()) : ?>
    <div class="search-results">
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-parts/content', 'list'); ?>
        <?php endwhile; ?>
    </div>

    <nav class="pagination">
        <?php echo paginate_links(['type' => 'list']); ?>
    </nav>
    <?php else : ?>
    <div class="no-results">
        <h2 class="text-headline-medium"><?php esc_html_e('未找到结果', 'flavor'); ?></h2>
        <p class="text-body-large text-on-surface-variant mt-8"><?php esc_html_e('试试其他关键词或浏览分类目录。', 'flavor'); ?></p>
        <div class="mt-24">
            <?php get_search_form(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
