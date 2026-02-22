<?php
/**
 * Flavor Theme - Author Card (displayed after post content)
 */
$author_id          = get_the_author_meta('ID');
$author_posts_count = count_user_posts($author_id);
$author_description = get_the_author_meta('description');

// 社交链接（复用 Customizer 设置）
$social_github  = get_theme_mod('flavor_social_github', '');
$social_twitter = get_theme_mod('flavor_social_twitter', '');
$social_email   = get_theme_mod('flavor_social_email', '');
$social_rss     = get_theme_mod('flavor_social_rss', '');
$has_social     = $social_github || $social_twitter || $social_email || $social_rss;
?>
<div class="author-card md-card-filled">
    <div class="author-card__header">
        <div class="author-card__avatar">
            <?php echo get_avatar($author_id, 64, '', '', ['class' => 'avatar-circle']); ?>
        </div>
        <div class="author-card__info">
            <h3 class="author-card__name">
                <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>"><?php the_author(); ?></a>
            </h3>
            <div class="author-card__meta">
                <?php printf(esc_html(_n('%d 篇文章', '%d 篇文章', $author_posts_count, 'flavor')), $author_posts_count); ?>
            </div>
        </div>
    </div>
    <?php if ($author_description) : ?>
    <p class="author-card__bio"><?php echo esc_html($author_description); ?></p>
    <?php endif; ?>
    <?php if ($has_social) : ?>
    <div class="author-card__social">
        <?php if ($social_github) : ?>
        <a href="<?php echo esc_url($social_github); ?>" class="author-social-link md-ripple" target="_blank" rel="noopener" aria-label="GitHub">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
        </a>
        <?php endif; ?>
        <?php if ($social_twitter) : ?>
        <a href="<?php echo esc_url($social_twitter); ?>" class="author-social-link md-ripple" target="_blank" rel="noopener" aria-label="Twitter">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        <?php endif; ?>
        <?php if ($social_email) : ?>
        <a href="mailto:<?php echo esc_attr($social_email); ?>" class="author-social-link md-ripple" aria-label="<?php esc_attr_e('邮箱', 'flavor'); ?>">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
        </a>
        <?php endif; ?>
        <?php if ($social_rss) : ?>
        <a href="<?php echo esc_url($social_rss); ?>" class="author-social-link md-ripple" target="_blank" rel="noopener" aria-label="RSS">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6.18 15.64a2.18 2.18 0 012.18 2.18C8.36 19 7.38 20 6.18 20 5 20 4 19 4 17.82a2.18 2.18 0 012.18-2.18M4 4.44A15.56 15.56 0 0119.56 20h-2.83A12.73 12.73 0 004 7.27V4.44m0 5.66a9.9 9.9 0 019.9 9.9h-2.83A7.07 7.07 0 004 12.93V10.1z"/></svg>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
