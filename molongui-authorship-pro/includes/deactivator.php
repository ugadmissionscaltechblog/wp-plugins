<?php

namespace Molongui\Authorship\Pro\Includes;
\defined( 'ABSPATH' ) or exit;
class Deactivator
{
    public static function deactivate( $network_wide )
    {
	    if ( \function_exists('is_multisite') and \is_multisite() and $network_wide )
	    {
		    if ( !\is_super_admin() ) return;
		    foreach ( \molongui_get_sites() as $site_id )
		    {
			    \switch_to_blog( $site_id );
				self::deactivate_single_blog();
			    \restore_current_blog();
		    }
	    }
	    else
	    {
		    if ( !\current_user_can( 'activate_plugins' ) ) return;

			self::deactivate_single_blog();
	    }
    }
	private static function deactivate_single_blog()
	{
        \remove_action( 'author_rewrite_rules', 'authorship_pro_update_guest_rewrite_rules' );
        \flush_rewrite_rules();
        $license = new \Molongui\Authorship\Pro\Includes\Update\License();
        $license->remove( false );
		\delete_transient( MOLONGUI_AUTHORSHIP_PRO_NAME.'-activated' );
		\delete_transient( MOLONGUI_AUTHORSHIP_PRO_NAME.'-updated' );
	}

} // class