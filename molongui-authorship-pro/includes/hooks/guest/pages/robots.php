<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_guest_pages_noindex( $robots )
{
    $options = Settings::get();

    if ( is_guest_author() and !empty( $options['guest_archive_noindex'] ) )
    {
        $robots['noindex']  = true;
    }

    return $robots;
}
add_filter( 'wp_robots', 'authorship_pro_guest_pages_noindex' );