<?php
// Material Design 3 Header
// 两级导航：桌面端 Top App Bar 水平导航，移动端汉堡菜单 + Modal Drawer
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="auto">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('site-layout md3-docs-layout'); ?>>
<?php wp_body_open(); ?>

<!-- Skip to content -->
<a class="skip-link" href="#main-content"><?php esc_html_e('跳至正文', 'flavor'); ?></a>

<div class="md3-layout-wrapper">
    <!-- Mobile Drawer Scrim -->
    <div class="drawer-scrim" aria-hidden="true"></div>

    <!-- Standard Drawer (Desktop) & Modal Drawer (Mobile) -->
    <nav class="nav-drawer md3-drawer--permanent" role="navigation" aria-label="<?php esc_attr_e('主导航', 'flavor'); ?>">
        <div class="nav-drawer__header">
            <div class="nav-drawer__brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="app-bar__brand" rel="home">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php endif; ?>
                    <span class="nav-drawer__site-name"><?php bloginfo('name'); ?></span>
                </a>
                <?php if (get_bloginfo('description')) : ?>
                <span class="nav-drawer__description"><?php bloginfo('description'); ?></span>
                <?php endif; ?>
            </div>
            <button class="md-top-app-bar__action md-ripple drawer-close-btn" aria-label="<?php esc_attr_e('关闭菜单', 'flavor'); ?>">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        </div>
        <div class="md-divider"></div>
        <div class="nav-drawer__content">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-drawer__menu',
                'fallback_cb'    => 'flavor_fallback_drawer_menu',
                'depth'          => 2,
            ]);
            ?>
        </div>
    </nav>

    <div class="md3-layout-main">
        <!-- Top App Bar -->
        <header class="site-header site-header--docs-layout" role="banner">
            <div class="app-bar">
                <!-- 汉堡菜单（移动端显示） -->
                <button class="app-bar__menu-btn md-top-app-bar__action md-ripple" aria-label="<?php esc_attr_e('打开菜单', 'flavor'); ?>" aria-expanded="false">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                    </svg>
                </button>

                <!-- 移动端站点品牌（由于左侧菜单此时被隐藏，所以显示这个品牌名） -->
                <a href="<?php echo esc_url(home_url('/')); ?>" class="app-bar__brand app-bar__brand--mobile-only" rel="home">
                    <span class="app-bar__site-name"><?php bloginfo('name'); ?></span>
                </a>

                <div class="app-bar__spacer"></div>

                <!-- 操作按钮 -->
                <div class="app-bar__actions">
                    <button class="md-top-app-bar__action md-ripple search-toggle-btn" aria-label="<?php esc_attr_e('搜索', 'flavor'); ?>">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                    </button>
                    <button class="md-top-app-bar__action md-ripple theme-toggle-btn" aria-label="<?php esc_attr_e('切换主题', 'flavor'); ?>">
                        <svg class="theme-icon theme-icon--light" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                            <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0a.996.996 0 000-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/>
                        </svg>
                        <svg class="theme-icon theme-icon--dark" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="display:none">
                            <path d="M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 01-4.4 2.26 5.403 5.403 0 01-3.14-9.8c-.44-.06-.9-.1-1.36-.1z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <?php if (is_single()) : ?>
        <!-- Reading Progress Bar -->
        <div class="reading-progress" aria-hidden="true">
            <div class="reading-progress__bar"></div>
        </div>
        <?php endif; ?>

        <!-- Search Overlay -->
        <div class="search-overlay" role="search" aria-hidden="true">
            <div class="search-overlay__bar">
                <button class="md-top-app-bar__action md-ripple search-close-btn" aria-label="<?php esc_attr_e('关闭搜索', 'flavor'); ?>">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                </button>
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" name="s" placeholder="<?php esc_attr_e('搜索文章...', 'flavor'); ?>" autocomplete="off" aria-label="<?php esc_attr_e('搜索', 'flavor'); ?>">
                </form>
            </div>
            <div class="search-overlay__suggestions"></div>
        </div>

        <!-- Main Content -->
        <main id="main-content" class="site-main" role="main">
