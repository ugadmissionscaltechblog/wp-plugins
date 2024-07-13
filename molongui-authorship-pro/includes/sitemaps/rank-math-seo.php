<?php

use Molongui\Authorship\Author;
use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'is_plugin_active' ) )
{
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

if ( is_plugin_active( 'seo-by-rank-math/rank-math.php' ) )
{
    add_filter( 'rank_math/sitemap/author_sitemap_url', function( $url, $sitemap )
    {
        $options = Settings::get();
        if ( !$options['user_archive_enabled'] ) return '';

        return $sitemap->sitemap_url( $url );
    }, 10, 2 );
    add_action( 'rank_math/sitemap/author_content', function()
    {
        if ( !Settings::get( 'guest_pages' ) )
        {
            return '';
        }
        $output = '';
        $guests = molongui_get_guests( array( 'fields' => 'all' ) );

        if ( !empty( $guests ) )
        {
            $chf = 'weekly';
            $pri = 0.5;

            foreach ( $guests as $guest )
            {
                $author = new Author( $guest->ID, 'guest', $guest );

                $url = array();
                if ( isset( $guest->post_modified_gmt ) and $guest->post_modified_gmt != '0000-00-00 00:00:00' and $guest->post_modified_gmt > $guest->post_date_gmt )
                {
                    $url['mod'] = $guest->post_modified_gmt;
                }
                else
                {
                    if ( '0000-00-00 00:00:00' != $guest->post_date_gmt ) $url['mod'] = $guest->post_date_gmt;
                    else $url['mod'] = $guest->post_date;
                }
                $url['loc'] = $author->get_url();
                $url['chf'] = $chf;
                $url['pri'] = $pri;
                $output    .= '<url>
							     <loc>'.$url['loc'].'</loc>
							     <lastmod>'.authorship_format_sitemap_date( $url['mod'] ).'</lastmod>
							   </url>';
            }
        }

        return $output;
    });
}