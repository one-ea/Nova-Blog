<?php
/**
 * Flavor Theme - Custom Comment Walker
 * Material Design 3 styled comments with card layout
 */
class Flavor_Walker_Comment extends Walker_Comment {

    protected function html5_comment( $comment, $depth, $args ) {
        $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
        ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'md-card-outlined comment-item', $comment ); ?>>
            <article class="comment-body">
                <header class="comment-header flex items-center gap-16">
                    <div class="comment-avatar">
                        <?php echo get_avatar( $comment, 48, '', '', [ 'class' => 'avatar-circle' ] ); ?>
                    </div>
                    <div class="comment-meta">
                        <span class="comment-author text-title-small"><?php comment_author_link( $comment ); ?></span>
                        <time class="comment-date text-label-medium text-on-surface-variant" datetime="<?php comment_time( 'c' ); ?>">
                            <?php
                            printf(
                                __( '%1$s 于 %2$s', 'flavor' ),
                                get_comment_date( '', $comment ),
                                get_comment_time()
                            );
                            ?>
                        </time>
                    </div>
                </header>

                <div class="comment-content text-body-medium">
                    <?php if ( '0' == $comment->comment_approved ) : ?>
                        <p class="comment-awaiting-moderation text-label-medium">
                            <?php esc_html_e( '您的评论正在等待审核。', 'flavor' ); ?>
                        </p>
                    <?php endif; ?>
                    <?php comment_text(); ?>
                </div>

                <footer class="comment-actions flex gap-8">
                    <?php
                    comment_reply_link( array_merge( $args, [
                        'add_below' => 'comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'before'    => '<span class="reply-link">',
                        'after'     => '</span>',
                    ] ) );
                    edit_comment_link( __( '编辑', 'flavor' ), '<span class="edit-link">', '</span>' );
                    ?>
                </footer>
            </article>
        <?php
    }
}
