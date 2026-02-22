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
        <div class="hero-section__avatar">
            <?php echo get_avatar(get_option('admin_email'), 80, '', get_bloginfo('name'), ['class' => 'avatar-circle']); ?>
        </div>
        <h1 class="hero-section__title"><?php bloginfo('name'); ?></h1>
        <p class="hero-section__subtitle"><?php bloginfo('description'); ?></p>
        <?php
        $social_github  = get_theme_mod('flavor_social_github', '');
        $social_twitter = get_theme_mod('flavor_social_twitter', '');
        $social_email   = get_theme_mod('flavor_social_email', '');
        $social_rss     = get_theme_mod('flavor_social_rss', '');
        if ($social_github || $social_twitter || $social_email || $social_rss) :
        ?>
        <div class="hero-section__social">
            <?php if ($social_github) : ?>
            <a href="<?php echo esc_url($social_github); ?>" class="hero-social-link md-ripple" target="_blank" rel="noopener" aria-label="GitHub">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
            </a>
            <?php endif; ?>
            <?php if ($social_twitter) : ?>
            <a href="<?php echo esc_url($social_twitter); ?>" class="hero-social-link md-ripple" target="_blank" rel="noopener" aria-label="Twitter">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <?php endif; ?>
            <?php if ($social_email) : ?>
            <a href="mailto:<?php echo esc_attr($social_email); ?>" class="hero-social-link md-ripple" aria-label="<?php esc_attr_e('邮箱', 'flavor'); ?>">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            </a>
            <?php endif; ?>
            <?php if ($social_rss) : ?>
            <a href="<?php echo esc_url($social_rss); ?>" class="hero-social-link md-ripple" target="_blank" rel="noopener" aria-label="RSS">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M6.18 15.64a2.18 2.18 0 012.18 2.18C8.36 19 7.38 20 6.18 20 5 20 4 19 4 17.82a2.18 2.18 0 012.18-2.18M4 4.44A15.56 15.56 0 0119.56 20h-2.83A12.73 12.73 0 004 7.27V4.44m0 5.66a9.9 9.9 0 019.9 9.9h-2.83A7.07 7.07 0 004 12.93V10.1z"/></svg>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
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

    <!-- Posts: 1-2-3 Column Layout -->
    <?php if (have_posts()) : ?>
    <?php $total_posts = $wp_query->post_count; ?>

    <?php if ($total_posts >= 4) : ?>
        <!-- Hero: 单列全宽 -->
        <?php the_post(); ?>
        <div class="posts-hero">
            <?php get_template_part('template-parts/content', 'card'); ?>
        </div>

        <!-- Duo: 双列 -->
        <div class="posts-duo">
            <?php for ($i = 0; $i < 2 && have_posts(); $i++) : the_post(); ?>
                <?php get_template_part('template-parts/content', 'card'); ?>
            <?php endfor; ?>
        </div>

        <!-- Grid: 三列 -->
        <?php if (have_posts()) : ?>
        <div class="posts-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', 'card'); ?>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    <?php else : ?>
        <!-- 文章较少时使用自适应网格 -->
        <div class="posts-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', 'card'); ?>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

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
