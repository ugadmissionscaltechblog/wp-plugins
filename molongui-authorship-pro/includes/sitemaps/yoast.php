<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'is_plugin_active' ) ) require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) or is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) )
{
    $options = authorship_get_options();
    if ( $options['guest_authors'] and $options['guest_pages'] )
    {
        /*!
         * FILTER HOOK
         *
         * Allows preventing the guest_author sitemap to be added to the Yoast SEO main sitemap index.
         *
         * @since 1.6.3
         */
        if ( !apply_filters( 'authorship_pro/add_guest_author_sitemap_to_yoast', true ) )
        {
            return;
        }

        add_filter( 'wpseo_sitemap_index', 'molongui_add_guests_sitemap_to_index', 99 );
        add_action( 'init', 'molongui_add_guests_sitemap_to_wpseo' );
    }
    function molongui_make_guest_cpt_wpseo_accessible( $post_types )
    {
        $post_types[] = 'guest_author';
        return $post_types;
    }
    function molongui_add_guests_sitemap_to_index( $smp )
    {
        global $wpseo_sitemaps;

        if ( !isset( $wpseo_sitemaps ) or empty( $wpseo_sitemaps ) ) return $smp;
        add_filter( 'wpseo_accessible_post_types', 'molongui_make_guest_cpt_wpseo_accessible' );
        $date = $wpseo_sitemaps->get_last_modified( 'guest_author' );
        remove_filter( 'wpseo_accessible_post_types', 'molongui_make_guest_cpt_wpseo_accessible' );

        $smp .= '<sitemap>' . PHP_EOL;
        $smp .= '<loc>' . site_url() .'/guest-author-sitemap.xml</loc>' . PHP_EOL;
        $smp .= '<lastmod>' . htmlspecialchars( $date ) . '</lastmod>' . PHP_EOL;
        $smp .= '</sitemap>' . PHP_EOL;

        return $smp;
    }
    function molongui_add_guests_sitemap_to_wpseo()
    {
        add_action( "wpseo_do_sitemap_guest-author", 'molongui_generate_guests_sitemap');
    }
    function molongui_generate_guests_sitemap()
    {
        global $wpseo_sitemaps;
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
                $output    .= $wpseo_sitemaps->renderer->sitemap_url( $url );
            }
        }

        if ( empty( $output ) )
        {
            $wpseo_sitemaps->bad_sitemap = true;
            return;
        }
        $sitemap  = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $sitemap .= $output . '</urlset>';

        $wpseo_sitemaps->set_sitemap( $sitemap );
    }

} // is_plugin_active