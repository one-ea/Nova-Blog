<?php
/**
 * Template Name: About Page
 * Slug: about
 *
 * 关于页专用模板 — Hero 头像 + 社交链接 + 兴趣标签 + 正文内容
 */
get_header();

// 社交链接（复用 Customizer 设置）
$social_github  = get_theme_mod('flavor_social_github', '');
$social_twitter = get_theme_mod('flavor_social_twitter', '');
$social_email   = get_theme_mod('flavor_social_email', '');
$social_rss     = get_theme_mod('flavor_social_rss', '');
$has_social     = $social_github || $social_twitter || $social_email || $social_rss;

// 兴趣标签（可在 Customizer 中配置，逗号分隔）
$interests_raw = get_theme_mod('flavor_about_interests', '');
$interests = array_filter(array_map('trim', explode(',', $interests_raw)));
?>

<div class="container content-area">

    <?php while (have_posts()) : the_post(); ?>

    <!-- About Hero -->
    <section class="about-hero">
        <div class="hero-blobs" aria-hidden="true">
            <div class="hero-blob hero-blob--1"></div>
            <div class="hero-blob hero-blob--2"></div>
            <div class="hero-blob hero-blob--3"></div>
        </div>

        <div class="about-hero__avatar">
            <?php echo get_avatar(get_the_author_meta('ID'), 120, '', get_the_author(), ['class' => 'avatar-circle']); ?>
        </div>

        <h1 class="about-hero__name"><?php the_title(); ?></h1>

        <?php
        $author_bio = get_the_author_meta('description');
        if ($author_bio) :
        ?>
        <p class="about-hero__bio"><?php echo esc_html($author_bio); ?></p>
        <?php endif; ?>

        <?php if ($has_social) : ?>
        <div class="about-hero__social">
            <?php if ($social_github) : ?>
            <a href="<?php echo esc_url($social_github); ?>" class="hero-social-link md-ripple" target="_blank" rel="noopener" aria-label="GitHub">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
            </a>
            <?php endif; ?>
            <?php if ($social_twitter) : ?>
            <a href="<?php echo esc_url($social_twitter); ?>" class="hero-social-link md-ripple" target="_blank" rel="noopener" aria-label="Twitter">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <?php endif; ?>
            <?php if ($social_email) : ?>
            <a href="mailto:<?php echo esc_attr($social_email); ?>" class="hero-social-link md-ripple" aria-label="<?php esc_attr_e('邮箱', 'flavor'); ?>">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            </a>
            <?php endif; ?>
            <?php if ($social_rss) : ?>
            <a href="<?php echo esc_url($social_rss); ?>" class="hero-social-link md-ripple" target="_blank" rel="noopener" aria-label="RSS">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M6.18 15.64a2.18 2.18 0 012.18 2.18C8.36 19 7.38 20 6.18 20 5 20 4 19 4 17.82a2.18 2.18 0 012.18-2.18M4 4.44A15.56 15.56 0 0119.56 20h-2.83A12.73 12.73 0 004 7.27V4.44m0 5.66a9.9 9.9 0 019.9 9.9h-2.83A7.07 7.07 0 004 12.93V10.1z"/></svg>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>

    <?php if (!empty($interests)) : ?>
    <!-- 兴趣标签 -->
    <section class="about-interests">
        <h2 class="about-interests__title"><?php esc_html_e('兴趣与技能', 'flavor'); ?></h2>
        <div class="about-interests__chips">
            <?php foreach ($interests as $interest) : ?>
            <span class="md-chip-assist">
                <?php echo esc_html($interest); ?>
            </span>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- 正文内容（来自 WordPress 编辑器） -->
    <article id="post-<?php the_ID(); ?>" <?php post_class('about-content'); ?>>
        <div class="about-content__body entry-content">
            <?php the_content(); ?>
        </div>
    </article>

    <?php endwhile; ?>

</div>

<?php get_footer(); ?>
