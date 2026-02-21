<?php
/**
 * Flavor Theme - Search Bar template part
 */
?>
<form role="search" method="get" class="search-form md-search-bar" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <svg class="search-form__icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="color:var(--md-sys-color-on-surface-variant);flex-shrink:0;margin:0 12px;">
        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
    </svg>
    <input type="search" class="md-search-bar__input" placeholder="<?php esc_attr_e( '搜索...', 'flavor' ); ?>" value="<?php echo get_search_query(); ?>" name="s" aria-label="<?php esc_attr_e( '搜索', 'flavor' ); ?>">
</form>
