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

<?php wp_footer(); ?>
</body>
</html>
