<?php
// 大尺寸特色文章卡片，全宽，图片背景
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('featured-card ' . flavor_card_class() . ' md-ripple'); ?>>
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
        <?php else : ?>
        <div class="featured-card__gradient"></div>
        <?php endif; ?>

        <div class="featured-card__overlay">
            <div>
                <?php
                $categories = get_the_category();
                if ($categories) :
                    foreach (array_slice($categories, 0, 2) as $cat) :
                ?>
                <span class="md-chip-assist"><?php echo esc_html($cat->name); ?></span>
                <?php endforeach; endif; ?>
            </div>
            <h2><?php the_title(); ?></h2>
            <p><?php
                $excerpt = get_the_excerpt();
                $plain = wp_strip_all_tags($excerpt);
                echo esc_html(mb_substr($plain, 0, 80, 'UTF-8')) . (mb_strlen($plain, 'UTF-8') > 80 ? '...' : '');
            ?></p>
            <div class="featured-card__meta">
                <?php echo get_avatar(get_the_author_meta('ID'), 32, '', '', ['class' => 'avatar-circle']); ?>
                <span class="featured-card__author"><?php the_author(); ?></span>
                <span class="featured-card__separator">·</span>
                <span class="featured-card__date"><?php echo get_the_date(); ?></span>
            </div>
        </div>

    </a>
</article>
