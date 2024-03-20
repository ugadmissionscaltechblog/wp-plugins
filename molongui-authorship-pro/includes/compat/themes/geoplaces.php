<?php
defined( 'ABSPATH' ) or exit;
add_filter( 'authorship_pro/guest/archive/is_author', function( $default, $wp_query )
{
    if ( !empty( $wp_query->query_vars['guest-author-name'] ) ) return false;
    return $default;
}, 10, 2 );