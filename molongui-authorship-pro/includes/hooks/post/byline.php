<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_pro_enable_byline_main_author' ) )
{
    function authorship_pro_enable_byline_main_author( $default, $type )
    {
        $options = authorship_get_options();

        switch ( $type )
        {
            case 'user' : return empty( $options['user_archive_enabled'] );  break;
            case 'guest': return empty( $options['guest_pages'] ); break;
        }

        return empty( $options['guest_pages'] );
    }
    add_filter( '_authorship/filter/link/disable_main', 'authorship_pro_enable_byline_main_author', 10, 2 );
}