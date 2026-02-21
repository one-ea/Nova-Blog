<?php
// 首页/博客主页模板
get_header();
?>

<div class="container content-area">

    <?php if (is_home() && !is_paged()) : ?>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-blobs" aria-hidden="true">
            <div class="hero-blob hero-blob--1"></div>
            <div class="hero-blob hero-blob--2"></div>
            <div class="hero-blob hero-blob--3"></div>
            <div class="hero-blob hero-blob--4"></div>
            <div class="hero-blob hero-blob--5"></div>
        </div>
        <h1 class="hero-section__title"><?php bloginfo('name'); ?></h1>
        <p class="hero-section__subtitle"><?php bloginfo('description'); ?></p>
    </section>

    <?php
    // 置顶文章
    $sticky = get_option('sticky_posts');
    if (!empty($sticky)) :
        $sticky_query = new WP_Query([
            'post__in' => $sticky,
            'posts_per_page' => 1,
            'ignore_sticky_posts' => 1,
        ]);
        if ($sticky_query->have_posts()) :
            while ($sticky_query->have_posts()) : $sticky_query->the_post();
    ?>
    <section class="featured-post">
        <?php get_template_part('template-parts/content', 'featured'); ?>
    </section>
    <?php
            endwhile;
            wp_reset_postdata();
        endif;
    endif;
    ?>

    <!-- Category Filter Chips -->
    <div class="category-chips">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="md-chip-filter <?php echo !is_category() ? 'md-chip-filter--selected' : ''; ?>">
            <?php esc_html_e('全部', 'flavor'); ?>
        </a>
        <?php
        $categories = get_categories(['hide_empty' => true]);
        foreach ($categories as $cat) :
        ?>
        <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="md-chip-filter">
            <?php echo esc_html($cat->name); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Posts Grid -->
    <?php if (have_posts()) : ?>
    <div class="posts-grid">
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('template-parts/content', 'card'); ?>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <nav class="pagination" aria-label="<?php esc_attr_e('文章导航', 'flavor'); ?>">
        <?php
        echo paginate_links([
            'prev_text' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>',
            'next_text' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>',
            'type' => 'list',
        ]);
        ?>
    </nav>

    <?php else : ?>
    <div class="no-results">
        <h2 class="text-headline-medium"><?php esc_html_e('暂无文章', 'flavor'); ?></h2>
        <p class="text-body-large text-on-surface-variant"><?php esc_html_e('试试其他关键词或浏览分类目录。', 'flavor'); ?></p>
    </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
