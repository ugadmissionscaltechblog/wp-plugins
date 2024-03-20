<?php
defined( 'WP_UNINSTALL_PLUGIN' ) or exit;
if ( !current_user_can( 'activate_plugins' ) ) return;
if ( function_exists('is_multisite') and is_multisite() )
{
	foreach ( molongui_get_sites() as $site_id )
	{
		switch_to_blog( $site_id );
		authorship_pro_uninstall();
		restore_current_blog();
	}
}
else
{
	authorship_pro_uninstall();
}
function authorship_pro_uninstall()
{
	global $wpdb;
    if ( did_action( 'authorship/init' ) )
    {
        require_once plugin_dir_path( __FILE__ ) . 'config/plugin.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/autoloader.php';

        $license = new \Molongui\Authorship\Pro\Includes\Update\License();
        $license->remove( true );
    }
    $like = 'molongui_authorship_pro_'.'%';
    $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '{$like}';" );
	$like = '_site_transient_'.'molongui-authorship-pro'.'%';
	$wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '{$like}';" );
}