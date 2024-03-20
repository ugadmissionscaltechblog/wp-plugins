<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
if ( class_exists( 'WP_Sitemaps_Provider' ) )
{
    if ( !class_exists( 'WP_Sitemaps_Guest_Authors' ) )
    {
        class WP_Sitemaps_Guest_Authors extends WP_Sitemaps_Provider
        {
            public function __construct()
            {
                $this->name        = 'guests';       // Used as the public-facing name in URLs.
                $this->object_type = 'guest_author'; // Object type name (e.g. 'post', 'term', 'user').
            }
            public function get_url_list( $page_num, $object_subtype = '' )
            {
                $urls = array();
                $mod  = '';
                $chf  = 'weekly';
                $pri  = 0.5;
                $guests = molongui_get_guests( array( 'fields' => 'all' ) );

                foreach ( $guests as $guest )
                {
                    $author = new Author( $guest->ID, 'guest', $guest );

                    if ( isset( $guest->post_modified_gmt ) and $guest->post_modified_gmt != '0000-00-00 00:00:00' and $guest->post_modified_gmt > $guest->post_date_gmt )
                    {
                        $mod = $guest->post_modified_gmt;
                    }
                    else
                    {
                        if ( '0000-00-00 00:00:00' != $guest->post_date_gmt ) $mod = $guest->post_date_gmt;
                        else $mod = $guest->post_date;
                    }

                    $urls[] = array
                    (
                        'loc'        => $author->get_url(),
                        'lastmod'    => authorship_format_sitemap_date( $mod ), // Optional field
                        'changefreq' => $chf,                                   // Optional field
                        'priority'   => $pri,                                   // Optional field
                    );
                }

                return $urls;
            }
            public function get_max_num_pages( $object_subtype = '' )
            {
                $guests = molongui_get_guests( array( 'fields' => 'all' ) );

                return (int) ceil( count( $guests ) / wp_sitemaps_get_max_urls( $this->object_type ) );
            }
            public function get_name()
            {
                return $this->name;
            }

        } // class
    } // !class_exists
    add_filter( 'init', function()
    {
        $options = authorship_get_options();
        if ( !$options['guest_pages'] ) return;

        $provider = new WP_Sitemaps_Guest_Authors();
        wp_register_sitemap_provider( $provider->get_name(), $provider );
    });

} // class_exists