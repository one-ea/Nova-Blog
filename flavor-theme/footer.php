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
                    <h3 class="footer-default__section-title"><?php esc_html_e('Recent Posts', 'flavor'); ?></h3>
                    <ul>
                        <?php
                        $recent = new WP_Query(['posts_per_page' => 5, 'no_found_rows' => true]);
                        while ($recent->have_posts()) : $recent->the_post();
                        ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                </div>
                <div>
                    <h3 class="footer-default__section-title"><?php esc_html_e('Categories', 'flavor'); ?></h3>
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
                        esc_html__('© %1$s %2$s. Powered by %3$s & %4$s.', 'flavor'),
                        date('Y'),
                        get_bloginfo('name'),
                        '<a href="https://wordpress.org">WordPress</a>',
                        '<a href="#">Flavor Theme</a>'
                    );
                }
                ?>
            </div>
            <?php if (has_nav_menu('footer')) : ?>
            <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer navigation', 'flavor'); ?>">
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
<button class="back-to-top md-fab md-ripple" aria-label="<?php esc_attr_e('Back to top', 'flavor'); ?>">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
        <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
    </svg>
</button>

<!-- Snackbar Container -->
<div class="snackbar-container" aria-live="polite"></div>

<?php wp_footer(); ?>
</body>
</html>
