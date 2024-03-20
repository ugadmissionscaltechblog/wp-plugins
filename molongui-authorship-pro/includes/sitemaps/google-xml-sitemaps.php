<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'google-sitemap-generator/sitemap.php' ) )
{
    add_action( 'sm_buildmap', function()
    {
        $generatorObject = &GoogleSitemapGenerator::GetInstance();

        if ( $generatorObject != null )
        {
            $guests = molongui_get_guests( array( 'fields' => 'ids' ) );
            if ( !empty( $guests ) )
            {
                foreach ( $guests as $guest )
                {
                    $author = new Author( $guest, 'guest' );
                    $generatorObject->AddUrl( $author->get_url(), '', 'weekly', 0.5 );
                }
            }
        }
    });

} // is_plugin_active