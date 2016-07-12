<?php
/**
 * Add custom JS scripts in admin area.
 */
add_action('admin_footer', 'adminJs');
function adminJs()
{
    echo '<script type="text/javascript"></script>';
}

/**
 * Disable admin bar on front-end.
 */
add_filter('show_admin_bar', '__return_false');
add_action('wp_before_admin_bar_render', 'removeAdminBar');
function removeAdminBar()
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
}
