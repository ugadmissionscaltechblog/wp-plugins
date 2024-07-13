<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_edit_guest_rest_api_links' ) )
{
    function authorship_pro_edit_guest_rest_api_links( $route )
    {
        if ( is_guest_author() )
        {
            if ( authorship_is_feature_enabled( 'guest_in_api' ) )
            {
                global $wp_query;
                if ( isset( $wp_query->guest_author_id ) )
                {
                    $route = '/wp/v2/' . 'guests' . '/' . $wp_query->guest_author_id;
                }
                else
                {
                    $route = '';
                }
            }
            else
            {
                $route = '';
            }
        }

        return $route;
    }
    add_filter( 'rest_queried_resource_route', 'authorship_pro_edit_guest_rest_api_links' );
}