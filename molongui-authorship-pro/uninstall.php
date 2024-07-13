<?php

use Molongui\Authorship\Common\Utils\WP;
use Molongui\Authorship\Pro\Common\Modules\License;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
defined( 'WP_UNINSTALL_PLUGIN' ) or exit; // Exit if not called by WordPress
if ( dirname( WP_UNINSTALL_PLUGIN ) !== dirname( plugin_basename( __FILE__ ) ) )
{
    status_header( 404 );
    exit;
}
if ( !current_user_can( 'activate_plugins' ) ) return;
if ( function_exists( 'is_multisite' ) and is_multisite() )
{
    foreach ( WP::get_sites() as $site_id )
    {
        switch_to_blog( $site_id );
		molongui_authorship_pro_uninstall();
		restore_current_blog();
	}
}
else
{
	molongui_authorship_pro_uninstall();
}
function molongui_authorship_pro_uninstall()
{
	global $wpdb;
    if ( !class_exists( 'MolonguiAuthorshipPro' ) )
    {
        require_once 'molongui-authorship-pro.php';
    }
    defined( 'MOLONGUI_AUTHORSHIP_NAME'  ) or define( 'MOLONGUI_AUTHORSHIP_NAME' , 'molongui-authorship' );
    defined( 'MOLONGUI_AUTHORSHIP_TITLE' ) or define( 'MOLONGUI_AUTHORSHIP_TITLE', 'Molongui Authorship' );
    require_once plugin_dir_path( __FILE__ ) . 'common/autoloader.php';
    $license = new License();
    $license->remove( true );
    $like = 'molongui_authorship_pro_'.'%';
    $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '{$like}';" );
	$like = '_site_transient_'.'molongui-authorship-pro'.'%';
	$wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '{$like}';" );
}