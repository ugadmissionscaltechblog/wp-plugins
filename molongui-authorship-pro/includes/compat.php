<?php
defined( 'ABSPATH' ) or exit;
$plugin_compat = authorship_is_feature_enabled( 'plugin_compat' );
$theme_compat  = authorship_is_feature_enabled( 'theme_compat' );
if ( $plugin_compat )
{
    if ( !function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

    if ( is_plugin_active( 'pmpro-member-directory/pmpro-member-directory.php' ) ) require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat/plugins/pmpro-member-directory.php';
    if ( is_plugin_active( 'polylang/polylang.php' ) ) require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat/plugins/polylang.php';
    if ( is_plugin_active( 'relevanssi/relevanssi.php' ) ) require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat/plugins/relevanssi.php';
    if ( is_plugin_active( 'td-cloud-library/td-cloud-library.php' ) ) require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat/plugins/tagdiv-cloud-library.php';
    if ( is_plugin_active( 'wp-graphql/wp-graphql.php' ) ) require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat/plugins/wp-graphql.php';
    if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) or is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) ) require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat/plugins/yoast.php';
}
if ( $theme_compat )
{
    $theme = wp_get_theme();

    if     ( in_array( 'BuddyBoss Theme', array( $theme->name, $theme->parent_theme ) ) ) require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat/themes/buddyboss.php';
    elseif ( in_array( 'GeoPlaces'      , array( $theme->name, $theme->parent_theme ) ) ) require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat/themes/geoplaces.php';
}