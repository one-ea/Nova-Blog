<?php
if ( post_password_required() ) return;
?>
<section id="comments" class="comments-area mt-32">
<?php if ( have_comments() ) : ?>
    <h2 class="comments-title text-title-large mb-24">
        <?php
        $count = get_comments_number();
        printf( esc_html( _n( '%d Comment', '%d Comments', $count, 'flavor' ) ), $count );
        ?>
    </h2>
    <ol class="comment-list">
        <?php wp_list_comments( [ 'walker' => new Flavor_Walker_Comment(), 'avatar_size' => 40, 'style' => 'ol', 'short_ping' => true ] ); ?>
    </ol>
    <?php the_comments_navigation( [ 'prev_text' => __( '&larr; Older Comments', 'flavor' ), 'next_text' => __( 'Newer Comments &rarr;', 'flavor' ) ] ); ?>
    <?php if ( ! comments_open() ) : ?>
    <p class="no-comments text-body-medium text-on-surface-variant" style="padding:16px;background:var(--md-sys-color-surface-container);border-radius:var(--md-sys-shape-corner-medium);">
        <?php esc_html_e( 'Comments are closed.', 'flavor' ); ?>
    </p>
    <?php endif; ?>
<?php endif; ?>
<?php
comment_form( [
    'class_form'           => 'comment-form',
    'title_reply'          => '<span class="text-title-large">' . __( 'Leave a Comment', 'flavor' ) . '</span>',
    'title_reply_to'       => '<span class="text-title-large">' . __( 'Reply to %s', 'flavor' ) . '</span>',
    'comment_notes_before' => '<p class="comment-notes text-body-small text-on-surface-variant mb-16">' . __( 'Your email address will not be published. Required fields are marked *', 'flavor' ) . '</p>',
    'fields' => [
        'author' => '<div class="comment-form-field mb-16"><label for="author" class="text-label-large">' . __( 'Name', 'flavor' ) . ' *</label><input id="author" name="author" type="text" class="md-text-field" required></div>',
        'email'  => '<div class="comment-form-field mb-16"><label for="email" class="text-label-large">' . __( 'Email', 'flavor' ) . ' *</label><input id="email" name="email" type="email" class="md-text-field" required></div>',
        'url'    => '<div class="comment-form-field mb-16"><label for="url" class="text-label-large">' . __( 'Website', 'flavor' ) . '</label><input id="url" name="url" type="url" class="md-text-field"></div>',
    ],
    'comment_field'  => '<div class="comment-form-field mb-16"><label for="comment" class="text-label-large">' . __( 'Comment', 'flavor' ) . ' *</label><textarea id="comment" name="comment" class="md-text-field" rows="5" required></textarea></div>',
    'submit_button'  => '<button type="submit" class="md-button-filled md-ripple">%4$s</button>',
    'submit_field'   => '<div class="form-submit mt-16">%1$s %2$s</div>',
] );
?>
</section>
