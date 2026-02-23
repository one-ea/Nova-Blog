<?php
// M3 Elevated Card 样式的文章卡片
// 结构：封面图/占位 -> 分类 Chip -> 标题 -> 摘要 -> 作者头像+名字+日期
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(flavor_card_class() . ' post-card md-ripple'); ?>>
    <a href="<?php the_permalink(); ?>" class="post-card__link" aria-label="<?php the_title_attribute(); ?>">

        <?php if (has_post_thumbnail()) : ?>
        <div class="md-card__media post-card__media">
            <?php the_post_thumbnail('flavor-card', ['class' => 'post-card__image', 'loading' => 'lazy']); ?>
        </div>
        <?php else : ?>
        <div class="post-card__placeholder">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
            </svg>
        </div>
        <?php endif; ?>

        <div class="md-card__header post-card__header">
            <?php
            $categories = get_the_category();
            if ($categories) :
            ?>
            <div class="post-card__categories mb-8">
                <?php foreach (array_slice($categories, 0, 2) as $cat) : ?>
                <span class="md-chip-assist md-chip--small"><?php echo esc_html($cat->name); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h2 class="post-card__title text-title-large"><?php the_title(); ?></h2>
        </div>

        <div class="md-card__content post-card__content">
            <p class="post-card__excerpt text-body-medium text-on-surface-variant">
                <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
            </p>
        </div>

        <div class="post-card__footer">
            <?php echo get_avatar(get_the_author_meta('ID'), 32, '', '', ['class' => 'avatar-circle']); ?>
            <div class="post-card__footer-meta">
                <div class="post-card__footer-top">
                    <span class="post-card__author"><?php the_author(); ?></span>
                    <time class="post-card__date" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                </div>
                <div class="post-card__footer-bottom">
                    <span><?php echo esc_html(flavor_reading_time()); ?></span>
                    <span class="post-card__meta-sep">·</span>
                    <span><?php printf(__('%d 次浏览', 'flavor'), flavor_get_post_views()); ?></span>
                </div>
            </div>
        </div>

    </a>
</article>
