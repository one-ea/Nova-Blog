<?php
// M3 文档风格的扁平化文章列表项
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('doc-list-item md-ripple'); ?>>
    <a href="<?php the_permalink(); ?>" class="doc-list-item__link" aria-label="<?php the_title_attribute(); ?>">
        
        <div class="doc-list-item__content">
            <?php
            $categories = get_the_category();
            if ($categories) :
            ?>
            <div class="doc-list-item__categories">
                <?php foreach (array_slice($categories, 0, 2) as $cat) : ?>
                <span class="text-label-small text-primary"><?php echo esc_html($cat->name); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h2 class="doc-list-item__title text-title-large"><?php the_title(); ?></h2>
            
            <p class="doc-list-item__excerpt text-body-medium text-on-surface-variant">
                <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
            </p>
        </div>

        <div class="doc-list-item__meta text-body-small text-on-surface-variant">
            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
            <span class="meta-sep">·</span>
            <span><?php echo esc_html(flavor_reading_time()); ?></span>
        </div>

    </a>
</article>
