<?php
if ( post_password_required() ) return;
?>
<section id="comments" class="comments-area mt-32">
<?php if ( have_comments() ) : ?>
    <h2 class="comments-title text-title-large mb-24">
        <?php
        $count = get_comments_number();
        printf( esc_html( _n( '%d 条评论', '%d 条评论', $count, 'flavor' ) ), $count );
        ?>
    </h2>
    <ol class="comment-list">
        <?php wp_list_comments( [ 'walker' => new Flavor_Walker_Comment(), 'avatar_size' => 48, 'style' => 'ol', 'short_ping' => true ] ); ?>
    </ol>
    <?php the_comments_navigation( [ 'prev_text' => __( '&larr; 较早的评论', 'flavor' ), 'next_text' => __( '较新的评论 &rarr;', 'flavor' ) ] ); ?>
    <?php if ( ! comments_open() ) : ?>
    <p class="no-comments text-body-medium text-on-surface-variant comments-closed">
        <?php esc_html_e( '评论已关闭。', 'flavor' ); ?>
    </p>
    <?php endif; ?>
<?php endif; ?>
<?php
comment_form( [
    'class_form'           => 'comment-form',
    'title_reply'          => '<span class="text-title-large">' . __( '发表评论', 'flavor' ) . '</span>',
    'title_reply_to'       => '<span class="text-title-large">' . __( '回复 %s', 'flavor' ) . '</span>',
    'comment_notes_before' => '<p class="comment-notes text-body-small text-on-surface-variant mb-16">' . __( '您的邮箱地址不会被公开。必填项已用 * 标注', 'flavor' ) . '</p>',
    'fields' => [
        'author' => '<div class="comment-form-field mb-16"><label for="author" class="text-label-large">' . __( '姓名', 'flavor' ) . ' *</label><input id="author" name="author" type="text" class="md-text-field md-text-field--outlined" required></div>',
        'email'  => '<div class="comment-form-field mb-16"><label for="email" class="text-label-large">' . __( '邮箱', 'flavor' ) . ' *</label><input id="email" name="email" type="email" class="md-text-field md-text-field--outlined" required></div>',
        'url'    => '<div class="comment-form-field mb-16"><label for="url" class="text-label-large">' . __( '网站', 'flavor' ) . '</label><input id="url" name="url" type="url" class="md-text-field md-text-field--outlined"></div>',
    ],
    'comment_field'  => '<div class="comment-form-field mb-16"><label for="comment" class="text-label-large">' . __( '评论', 'flavor' ) . ' *</label><textarea id="comment" name="comment" class="md-text-field md-text-field--outlined" rows="5" required></textarea></div>',
    'submit_button'  => '<button type="submit" class="md-button-filled md-ripple">%4$s</button>',
    'submit_field'   => '<div class="form-submit mt-16">%1$s %2$s</div>',
] );
?>
</section>
