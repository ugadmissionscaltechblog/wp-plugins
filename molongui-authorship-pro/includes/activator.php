<?php

namespace Molongui\Authorship\Pro\Includes;
\defined( 'ABSPATH' ) or exit;
class Activator
{
    public static function activate( $network_wide )
    {
	    if ( \function_exists('is_multisite') and \is_multisite() and $network_wide )
	    {
		    if ( !\is_super_admin() ) return;
		    foreach ( \molongui_get_sites() as $site_id )
		    {
			    \switch_to_blog( $site_id );
			    self::activate_single_blog();
			    \restore_current_blog();
		    }
        }
        else
        {
	        if ( !\current_user_can( 'activate_plugins' ) ) return;

	        self::activate_single_blog();
        }
	    \set_transient( MOLONGUI_AUTHORSHIP_PRO_NAME.'-activated', 1 );
    }
	private static function activate_single_blog()
	{
        \wp_cache_flush();
        if ( \did_action( 'authorship/loaded' ) )
        {
            $update_db = new \Molongui\Authorship\Includes\Libraries\Common\DB_Update( MOLONGUI_AUTHORSHIP_PRO_DB, MOLONGUI_AUTHORSHIP_PRO_DB_VERSION, MOLONGUI_AUTHORSHIP_PRO_NAMESPACE );
            if ( $update_db->db_update_needed() ) $update_db->run_update();
        }
		self::add_default_options();
        $license = new \Molongui\Authorship\Pro\Includes\Update\License();
        $license->init();
        self::save_installation_data();
        self::add_contributors_page();
        update_option( MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_flush_rewrite_rules', 1, true );
	}
	public static function activate_on_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta )
	{
		if ( \is_plugin_active_for_network( MOLONGUI_AUTHORSHIP_PRO_BASENAME ) )
		{
			\switch_to_blog( $blog_id );
			self::activate_single_blog();
			\restore_current_blog();
		}
	}
	public static function save_installation_data()
	{
		if ( \get_option( MOLONGUI_AUTHORSHIP_PRO_INSTALL ) ) return;
		$installation = array
		(
			'timestamp' => \time(),
			'version'   => MOLONGUI_AUTHORSHIP_PRO_VERSION,
		);
		\add_option( MOLONGUI_AUTHORSHIP_PRO_INSTALL, $installation, null, 'no' );
	}
    public static function add_default_options()
    {

    }
	public static function add_contributors_page()
	{
		if ( \get_option( MOLONGUI_AUTHORSHIP_PRO_CONTRIB_ID ) ) return;
		$contributors = array
		(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_author'    => \get_current_user_id(),
			'meta_input'     => array
			(
				'_molongui_author_box_display' => 'hide',
			),
			'post_title'     => __( "Contributors", 'molongui-authorship-pro' ),
			'post_content'   => '<p>'.__( "Many people have contributed to this website and we are thankful to them all for their hard work.", 'molongui-authorship-pro' ).'</p> [molongui_author_list output=list layout=basic min_post_count=1]',
		);
		$contributors_id = \wp_insert_post( $contributors, true );
		if ( !\is_wp_error( $contributors_id ) )
		{
			\update_option( MOLONGUI_AUTHORSHIP_PRO_CONTRIB_ID, $contributors_id );
		}
	}

} // class