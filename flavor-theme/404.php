<?php
// 404 错误页面 — 花园主题插画 + 最近文章推荐
get_header();
?>
<div class="container content-area error-page">

    <!-- SVG 插画：一朵在花盆里枯萎的花 -->
    <div class="error-page__illustration" aria-hidden="true">
        <svg viewBox="0 0 200 180" width="200" height="180" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- 花盆 -->
            <path d="M70 130h60l-8 40H78l-8-40z" fill="var(--md-sys-color-tertiary-container)"/>
            <path d="M65 122h70a5 5 0 010 10H65a5 5 0 010-10z" fill="var(--md-sys-color-tertiary)"/>
            <!-- 茎 -->
            <path d="M100 122c0-30-15-50-10-70" stroke="var(--md-sys-color-primary)" stroke-width="3" stroke-linecap="round" fill="none"/>
            <!-- 枯萎的花瓣（下垂） -->
            <ellipse cx="82" cy="52" rx="14" ry="8" transform="rotate(40 82 52)" fill="var(--md-sys-color-primary-container)" opacity="0.8"/>
            <ellipse cx="78" cy="60" rx="12" ry="7" transform="rotate(70 78 60)" fill="var(--md-sys-color-primary-container)" opacity="0.6"/>
            <ellipse cx="96" cy="48" rx="13" ry="7" transform="rotate(-20 96 48)" fill="var(--md-sys-color-secondary-container)" opacity="0.7"/>
            <ellipse cx="102" cy="56" rx="11" ry="6" transform="rotate(-50 102 56)" fill="var(--md-sys-color-secondary-container)" opacity="0.5"/>
            <!-- 花蕊 -->
            <circle cx="90" cy="52" r="6" fill="var(--md-sys-color-primary)"/>
            <!-- 叶子 -->
            <path d="M95 95c-20-5-35 5-30 15 15-2 28-8 30-15z" fill="var(--md-sys-color-tertiary)" opacity="0.6"/>
            <path d="M92 105c15-10 35-8 32 5-12 2-27-1-32-5z" fill="var(--md-sys-color-tertiary)" opacity="0.5"/>
            <!-- 掉落的花瓣 -->
            <ellipse class="error-page__petal error-page__petal--1" cx="130" cy="118" rx="6" ry="3" transform="rotate(25 130 118)" fill="var(--md-sys-color-primary-container)" opacity="0.5"/>
            <ellipse class="error-page__petal error-page__petal--2" cx="55" cy="125" rx="5" ry="3" transform="rotate(-15 55 125)" fill="var(--md-sys-color-secondary-container)" opacity="0.4"/>
        </svg>
    </div>

    <h1 class="error-page__code">404</h1>
    <h2 class="text-headline-medium mb-8"><?php esc_html_e('页面未找到', 'flavor'); ?></h2>
    <p class="text-body-large text-on-surface-variant mb-24"><?php esc_html_e('这朵花似乎迷路了... 您访问的页面可能已被删除或暂时不可用。', 'flavor'); ?></p>

    <div class="error-page__search">
        <?php get_search_form(); ?>
    </div>

    <a href="<?php echo esc_url(home_url('/')); ?>" class="md-button-filled md-ripple">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="margin-right:8px"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <?php esc_html_e('返回首页', 'flavor'); ?>
    </a>

    <?php
    // 最近文章推荐
    $recent = new WP_Query([
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
    if ($recent->have_posts()) :
    ?>
    <section class="error-page__recent">
        <h3 class="error-page__recent-title"><?php esc_html_e('也许你在找这些？', 'flavor'); ?></h3>
        <div class="error-page__recent-grid">
            <?php while ($recent->have_posts()) : $recent->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="error-page__recent-item md-card-outlined md-ripple">
                <span class="text-title-medium"><?php the_title(); ?></span>
                <time class="text-label-medium text-on-surface-variant" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
            </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

</div>
<?php get_footer(); ?>
