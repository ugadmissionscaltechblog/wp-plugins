<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_user_pages_noindex( $robots )
{
    $options = authorship_get_options();

    if ( is_author() and !is_guest_author() and !empty( $options['user_archive_noindex'] ) )
    {
        $robots['noindex']  = true;
    }

    return $robots;
}
add_filter( 'wp_robots', 'authorship_pro_user_pages_noindex' );