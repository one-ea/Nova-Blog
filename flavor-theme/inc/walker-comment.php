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
                    <?php $comment_likes = flavor_get_comment_likes( get_comment_ID() ); ?>
                    <button class="comment-like-btn" data-comment-id="<?php comment_ID(); ?>" aria-label="<?php esc_attr_e( '点赞评论', 'flavor' ); ?>">
                        <svg class="comment-like-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/></svg>
                        <span class="comment-like-count"><?php echo $comment_likes > 0 ? esc_html( $comment_likes ) : ''; ?></span>
                    </button>
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
