<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_enable_byline_main_author' ) )
{
    function authorship_pro_enable_byline_main_author( $default, $type )
    {
        $options = Settings::get();

        switch ( $type )
        {
            case 'user' : return empty( $options['user_archive_enabled'] );  break;
            case 'guest': return empty( $options['guest_pages'] ); break;
        }

        return empty( $options['guest_pages'] );
    }
    add_filter( '_authorship/filter/link/disable_main', 'authorship_pro_enable_byline_main_author', 10, 2 );
}