<?php
// 文章详情页模板
get_header();

while (have_posts()) : the_post();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

    <!-- 文章内容区（头部 + 正文 + TOC 共享同一 flex 布局，确保对齐） -->
    <div class="container">
        <div class="post-content-wrapper">
            <div class="post-content">

                <!-- 文章头部 -->
                <header class="post-header">
                    <?php flavor_breadcrumbs(); ?>

                    <div class="post-categories">
                        <?php
                        $categories = get_the_category();
                        foreach ($categories as $cat) :
                        ?>
                        <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="md-chip-assist">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>

                    <h1 class="post-title"><?php the_title(); ?></h1>

                    <div class="post-meta">
                        <div class="post-author-avatar">
                            <?php echo get_avatar(get_the_author_meta('ID'), 40, '', '', ['class' => 'avatar-circle']); ?>
                        </div>
                        <div class="post-meta-text">
                            <span class="post-author-name">
                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                    <?php the_author(); ?>
                                </a>
                            </span>
                            <div class="post-meta-details">
                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                <?php if (get_theme_mod('flavor_show_reading_time', true)) : ?>
                                <span class="meta-separator">·</span>
                                <span><?php echo esc_html(flavor_reading_time()); ?></span>
                                <?php endif; ?>
                                <span class="meta-separator">·</span>
                                <span><?php printf(__('%d 次浏览', 'flavor'), flavor_get_post_views()); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('full', ['class' => 'post-cover-image']); ?>
                    </div>
                    <?php endif; ?>
                </header>

                <!-- 正文 -->
                <div class="entry-content">
                    <?php the_content(); ?>
                    <?php
                    wp_link_pages([
                        'before' => '<nav class="page-links flex gap-8 mt-24"><span class="text-label-large">' . __('页码：', 'flavor') . '</span>',
                        'after' => '</nav>',
                    ]);
                    ?>
                </div>

            </div>

            <?php if (get_theme_mod('flavor_show_toc', true)) : ?>
            <aside class="post-toc">
                <div class="toc-container"></div>
            </aside>
            <?php endif; ?>
        </div>

        <!-- 文章底部（与 post-content 共享同一 container，自然左对齐） -->
        <div class="post-bottom">

            <?php $tags = get_the_tags(); if ($tags) : ?>
            <div class="post-tags">
                <?php foreach ($tags as $tag) : ?>
                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="md-chip-assist">
                    #<?php echo esc_html($tag->name); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="post-actions">
                <div class="post-actions__left">
                    <button class="md-button-tonal md-ripple post-like-btn">
                        <svg class="like-icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/></svg>
                        <span><?php esc_html_e('点赞', 'flavor'); ?></span>
                        <?php $likes = flavor_get_post_likes(); ?>
                        <span class="like-count"><?php echo $likes > 0 ? esc_html($likes) : ''; ?></span>
                    </button>
                    <button class="md-button-tonal md-ripple post-share-btn">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>
                        <span><?php esc_html_e('分享', 'flavor'); ?></span>
                    </button>
                </div>
                <div class="post-actions__right">
                    <?php $prev_post = get_previous_post(); $next_post = get_next_post(); ?>
                    <?php if ($prev_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="md-button-text md-ripple" title="<?php echo esc_attr($prev_post->post_title); ?>">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                        <span><?php esc_html_e('上一篇', 'flavor'); ?></span>
                    </a>
                    <?php endif; ?>
                    <?php if ($next_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="md-button-text md-ripple" title="<?php echo esc_attr($next_post->post_title); ?>">
                        <span><?php esc_html_e('下一篇', 'flavor'); ?></span>
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php get_template_part('template-parts/author', 'card'); ?>

            <?php
            $related_count = get_theme_mod('flavor_related_posts_count', 3);
            if ($related_count > 0) :
                $cats = wp_get_post_categories(get_the_ID());
                $related = new WP_Query([
                    'category__in' => $cats,
                    'post__not_in' => [get_the_ID()],
                    'posts_per_page' => $related_count,
                    'orderby' => 'rand',
                ]);
                if ($related->have_posts()) :
            ?>
            <section class="related-posts">
                <h2 class="related-posts__title"><?php esc_html_e('相关文章', 'flavor'); ?></h2>
                <div class="doc-list">
                    <?php while ($related->have_posts()) : $related->the_post(); ?>
                        <?php get_template_part('template-parts/content', 'card'); ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
            <?php endif; endif; ?>

            <?php if (is_active_sidebar('after-post')) : ?>
            <div class="after-post-widgets mb-32">
                <?php dynamic_sidebar('after-post'); ?>
            </div>
            <?php endif; ?>

            <?php if (comments_open() || get_comments_number()) : ?>
                <?php comments_template(); ?>
            <?php endif; ?>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
