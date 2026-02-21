<?php
/**
 * Flavor Theme - Author Card (displayed after post content)
 */
$author_id          = get_the_author_meta( 'ID' );
$author_posts_count = count_user_posts( $author_id );
$author_description = get_the_author_meta( 'description' );
?>
<div class="author-card md-card-filled mb-32">
    <div class="flex items-center gap-16">
        <div class="author-card__avatar">
            <?php echo get_avatar( $author_id, 64, '', '', [ 'class' => 'avatar-circle' ] ); ?>
        </div>
        <div class="author-card__info">
            <h3 class="text-title-large">
                <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php the_author(); ?></a>
            </h3>
            <?php if ( $author_description ) : ?>
            <p class="text-body-medium text-on-surface-variant mt-4"><?php echo esc_html( $author_description ); ?></p>
            <?php endif; ?>
            <div class="text-label-medium text-on-surface-variant mt-8">
                <?php printf( esc_html( _n( '%d 篇文章', '%d 篇文章', $author_posts_count, 'flavor' ) ), $author_posts_count ); ?>
            </div>
        </div>
    </div>
</div>
