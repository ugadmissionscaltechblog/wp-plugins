<?php

use Molongui\Authorship\Author;
use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name )
{
    if ( 'users' != $name ) return $provider;

    $options = Settings::get();
    if ( !$options['user_archive_enabled'] ) $provider = null;

    return $provider;
}, 10, 2 );