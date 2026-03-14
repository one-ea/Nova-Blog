<?php
// Material Design 3 Footer
// 结构：关闭 main → Footer（三列 widget + 版权 + 导航）→ FAB → Snackbar → wp_footer
?>
</main><!-- .site-main -->

<!-- Footer -->
<footer class="site-footer" role="contentinfo">
    <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3')) : ?>
    <div class="footer-widgets">
        <div class="container">
            <div class="footer-widgets__grid">
                <?php if (is_active_sidebar('footer-1')) : ?>
                <div class="footer-column"><?php dynamic_sidebar('footer-1'); ?></div>
                <?php endif; ?>
                <?php if (is_active_sidebar('footer-2')) : ?>
                <div class="footer-column"><?php dynamic_sidebar('footer-2'); ?></div>
                <?php endif; ?>
                <?php if (is_active_sidebar('footer-3')) : ?>
                <div class="footer-column"><?php dynamic_sidebar('footer-3'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else : ?>
    <!-- Footer 默认内容（未配置 widget 时显示） -->
    <div class="footer-default">
        <div class="container">
            <div class="footer-default__grid">
                <div>
                    <h3 class="footer-default__section-title"><?php bloginfo('name'); ?></h3>
                    <p class="footer-default__description"><?php bloginfo('description'); ?></p>
                </div>
                <div>
                    <h3 class="footer-default__section-title"><?php esc_html_e('最新文章', 'flavor'); ?></h3>
                    <ul>
                        <?php
                        // Transient 缓存，1 小时过期，减少数据库查询
                        $recent_posts = get_transient('flavor_footer_recent');
                        if (false === $recent_posts) {
                            $recent_posts = get_posts([
                                'numberposts'  => 5,
                                'post_status'  => 'publish',
                                'no_found_rows' => true,
                            ]);
                            set_transient('flavor_footer_recent', $recent_posts, HOUR_IN_SECONDS);
                        }
                        foreach ($recent_posts as $rpost) :
                        ?>
                        <li><a href="<?php echo esc_url(get_permalink($rpost)); ?>"><?php echo esc_html($rpost->post_title); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="footer-default__section-title"><?php esc_html_e('分类目录', 'flavor'); ?></h3>
                    <ul>
                        <?php wp_list_categories(['title_li' => '', 'show_count' => true]); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="footer-bottom">
            <div class="footer-copyright">
                <?php
                $footer_text = get_theme_mod('flavor_footer_text', '');
                if ($footer_text) {
                    echo wp_kses_post($footer_text);
                } else {
                    printf(
                        esc_html__('© %1$s %2$s. 由 %3$s 和 %4$s 驱动。', 'flavor'),
                        date('Y'),
                        get_bloginfo('name'),
                        '<a href="https://wordpress.org">WordPress</a>',
                        '<a href="https://github.com/flavor-theme">Flavor Theme</a>'
                    );
                }
                ?>
            </div>
            <?php if (has_nav_menu('footer')) : ?>
            <nav class="footer-nav" aria-label="<?php esc_attr_e('页脚导航', 'flavor'); ?>">
                <?php wp_nav_menu([
                    'theme_location' => 'footer',
                    'container'      => false,
                    'menu_class'     => 'footer-menu',
                    'depth'          => 1,
                ]); ?>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</footer>

<!-- Back to Top FAB -->
<button class="back-to-top md-fab md-ripple" aria-label="<?php esc_attr_e('回到顶部', 'flavor'); ?>">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
        <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
    </svg>
</button>

<!-- Snackbar Container -->
<div class="snackbar-container" aria-live="polite"></div>

<!-- Bottom Navigation (Mobile only, hidden via CSS on ≥600px) -->
<nav class="md-navigation-bar bottom-nav" aria-label="<?php esc_attr_e('底部导航', 'flavor'); ?>">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="md-navigation-bar__item<?php echo is_front_page() ? ' md-navigation-bar__item--active' : ''; ?>">
        <span class="md-navigation-bar__icon">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        </span>
        <span class="md-navigation-bar__label"><?php esc_html_e('首页', 'flavor'); ?></span>
    </a>
    <button class="md-navigation-bar__item" id="bottom-nav-search" type="button">
        <span class="md-navigation-bar__icon">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        </span>
        <span class="md-navigation-bar__label"><?php esc_html_e('搜索', 'flavor'); ?></span>
    </button>
    <button class="md-navigation-bar__item" id="bottom-nav-menu" type="button">
        <span class="md-navigation-bar__icon">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        </span>
        <span class="md-navigation-bar__label"><?php esc_html_e('菜单', 'flavor'); ?></span>
    </button>
    <button class="md-navigation-bar__item" id="bottom-nav-top" type="button">
        <span class="md-navigation-bar__icon">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
        </span>
        <span class="md-navigation-bar__label"><?php esc_html_e('顶部', 'flavor'); ?></span>
    </button>
</nav>

<?php wp_footer(); ?>
    </div><!-- .md3-layout-main -->
</div><!-- .md3-layout-wrapper -->
</body>
</html>
