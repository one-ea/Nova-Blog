<?php
// 水平布局的文章列表项（搜索结果用）
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('md-card-outlined post-list-item'); ?>>
    <a href="<?php the_permalink(); ?>" class="post-list-item__link" aria-label="<?php the_title_attribute(); ?>">

        <?php if (has_post_thumbnail()) : ?>
        <div class="post-list-item__media">
            <?php the_post_thumbnail('flavor-card-small', ['class' => 'post-list-item__image', 'loading' => 'lazy']); ?>
        </div>
        <?php endif; ?>

        <div class="post-list-item__content">
            <div class="post-card__categories">
                <?php
                $categories = get_the_category();
                if ($categories) :
                    foreach (array_slice($categories, 0, 2) as $cat) :
                ?>
                <span class="md-chip-assist md-chip--small"><?php echo esc_html($cat->name); ?></span>
                <?php endforeach; endif; ?>
            </div>

            <h3 class="post-list-item__title"><?php the_title(); ?></h3>

            <p class="post-list-item__excerpt">
                <?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?>
            </p>

            <div class="post-list-item__meta">
                <?php echo get_avatar(get_the_author_meta('ID'), 20, '', '', ['class' => 'avatar-circle']); ?>
                <span><?php the_author(); ?></span>
                <span>·</span>
                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                <?php if (get_theme_mod('flavor_show_reading_time', true)) : ?>
                <span>·</span>
                <span><?php echo esc_html(flavor_reading_time()); ?></span>
                <?php endif; ?>
            </div>
        </div>

    </a>
</article>
