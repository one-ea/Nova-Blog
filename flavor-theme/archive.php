<?php
get_header();
?>
<div class="container content-area">
    <?php flavor_breadcrumbs(); ?>

    <header class="archive-header mb-32">
        <?php
        the_archive_title('<h1 class="text-headline-large">', '</h1>');
        the_archive_description('<p class="text-body-large text-on-surface-variant mt-8">', '</p>');
        ?>
    </header>

    <?php if (is_category()) : ?>
    <div class="category-chips">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="md-chip-filter"><?php esc_html_e('All', 'flavor'); ?></a>
        <?php
        $categories = get_categories(['hide_empty' => true]);
        foreach ($categories as $cat) :
            $is_current = is_category($cat->term_id) ? 'md-chip-filter--selected' : '';
        ?>
        <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="md-chip-filter <?php echo $is_current; ?>">
            <?php echo esc_html($cat->name); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (have_posts()) : ?>
    <div class="posts-grid">
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-parts/content', 'card'); ?>
        <?php endwhile; ?>
    </div>

    <nav class="pagination" aria-label="<?php esc_attr_e('Posts navigation', 'flavor'); ?>">
        <?php echo paginate_links([
            'prev_text' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>',
            'next_text' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>',
            'type' => 'list',
        ]); ?>
    </nav>
    <?php else : ?>
    <div class="no-results">
        <h2 class="text-headline-medium"><?php esc_html_e('No posts found', 'flavor'); ?></h2>
    </div>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
