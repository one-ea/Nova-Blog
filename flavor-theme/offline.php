<?php
/**
 * Offline fallback page
 * 当用户离线且缓存中没有目标页面时显示
 */
get_header();
?>
<div class="container content-area" style="text-align:center; padding: 80px 24px;">
    <svg viewBox="0 0 24 24" width="64" height="64" fill="var(--md-sys-color-on-surface-variant)" style="margin-bottom:24px;">
        <path d="M24 8.98C20.93 5.9 16.69 4 12 4c-1.21 0-2.4.13-3.53.37L20.49 16.4 24 8.98zM2.92 2.51L1.51 3.93l2.36 2.36C1.65 8.29 0 11.07 0 8.98l2.92 2.93C5.03 8.8 8.35 7 12 7c1.59 0 3.09.37 4.44 1.01l1.68 1.68C16.64 8.56 14.39 7.89 12 7.89c-3.31 0-6 2.69-6 6 0 .79.16 1.54.44 2.23l2.03 2.03c-.29-.7-.47-1.46-.47-2.26 0-2.21 1.79-4 4-4 .8 0 1.56.18 2.26.47l1.68 1.68-.23.23C14.64 15.35 13.38 16 12 16c-2.21 0-4-1.79-4-4 0-.36.08-.7.18-1.03l-5.26-5.26L1.51 3.93z"/>
    </svg>
    <h1 class="text-headline-large" style="margin-bottom:16px;">
        <?php esc_html_e('您当前离线', 'flavor'); ?>
    </h1>
    <p class="text-body-large text-on-surface-variant" style="margin-bottom:32px;">
        <?php esc_html_e('似乎您的网络连接已断开，请检查网络后重试。', 'flavor'); ?>
    </p>
    <button onclick="window.location.reload()" class="md-filled-button" style="cursor:pointer;">
        <?php esc_html_e('重试', 'flavor'); ?>
    </button>
</div>
<?php get_footer(); ?>
