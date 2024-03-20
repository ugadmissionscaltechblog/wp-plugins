<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name )
{
    if ( 'users' != $name ) return $provider;

    $options = authorship_get_options();
    if ( !$options['user_archive_enabled'] ) $provider = null;

    return $provider;
}, 10, 2 );