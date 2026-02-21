<?php
// 大尺寸特色文章卡片，全宽，图片背景
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('featured-card md-card-elevated md-ripple'); ?>>
    <a href="<?php the_permalink(); ?>" class="featured-card__link">

        <?php if (has_post_thumbnail()) : ?>
        <div class="featured-card__media">
            <?php the_post_thumbnail('full', [
                'class'         => 'featured-card__image',
                'loading'       => 'eager',
                'fetchpriority' => 'high',
                'decoding'      => 'async',
            ]); ?>
        </div>
        <?php endif; ?>

        <div class="featured-card__overlay">
            <div class="mb-8">
                <?php
                $categories = get_the_category();
                if ($categories) :
                    foreach (array_slice($categories, 0, 2) as $cat) :
                ?>
                <span class="md-chip-assist"><?php echo esc_html($cat->name); ?></span>
                <?php endforeach; endif; ?>
            </div>
            <h2 class="text-headline-large"><?php the_title(); ?></h2>
            <p class="text-body-large"><?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?></p>
            <div class="flex items-center gap-8 mt-16">
                <?php echo get_avatar(get_the_author_meta('ID'), 32, '', '', ['class' => 'avatar-circle']); ?>
                <span class="text-label-large" style="color: #fff;"><?php the_author(); ?></span>
                <span style="color: rgba(255,255,255,0.7);">·</span>
                <span class="text-label-medium" style="color: rgba(255,255,255,0.7);"><?php echo get_the_date(); ?></span>
            </div>
        </div>

    </a>
</article>
