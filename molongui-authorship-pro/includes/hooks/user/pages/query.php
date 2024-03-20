<?php
defined( 'ABSPATH' ) or exit;
if ( !molongui_is_request( 'frontend' ) ) return;
if ( !function_exists( 'authorship_pro_add_author_pages' ) )
{
    function authorship_pro_add_author_pages( $wp_query )
    {
        $options = authorship_get_options();
        if ( empty( $options['user_archive_include_pages'] ) ) return;
        if ( ( is_admin() and ( !defined( 'DOING_AJAX' ) or !DOING_AJAX /*or !wp_doing_ajax()*/ ) ) //is_admin()
            or ( !$wp_query->is_main_query() and apply_filters_ref_array( 'molongui_edit_main_query_only', array( true, &$wp_query ) ) )
        ) return;
        if ( $wp_query->is_author() and !$wp_query->is_guest_author )
        {
            $post_types = $wp_query->get( 'post_type' );
            if ( !is_array( $post_types ) and !empty( $post_types ) ) $post_types = explode( ',', $post_types );
            if ( empty( $post_types ) )
            {
                $post_types   = array();
                $post_types[] = 'post';
            }
            if ( !in_array( 'page', $post_types ) ) $post_types[] = 'page';
            $post_types = array_map( 'trim', $post_types );
            $post_types = array_filter( $post_types );
            $wp_query->set( 'post_type', $post_types );
        }
    }
    add_action( 'pre_get_posts', 'authorship_pro_add_author_pages', 999 );
}
if ( !function_exists( 'authorship_pro_add_author_cpts' ) )
{
    function authorship_pro_add_author_cpts( $wp_query )
    {
        $options = authorship_get_options();
        if ( empty( $options['user_archive_include_cpts'] ) ) return;
        if ( ( is_admin() and ( !defined( 'DOING_AJAX' ) or !DOING_AJAX /*or !wp_doing_ajax()*/ ) ) //is_admin()
            or ( !$wp_query->is_main_query() and apply_filters_ref_array( 'molongui_edit_main_query_only', array( true, &$wp_query ) ) )
        ) return;
        if ( $wp_query->is_author() and !$wp_query->is_guest_author )
        {
            $post_types = $wp_query->get( 'post_type' );
            if ( !is_array( $post_types ) and !empty( $post_types ) ) $post_types = explode( ',', $post_types );
            if ( empty( $post_types ) )
            {
                $post_types   = array();
                $post_types[] = 'post';
            }
            $cpts = array_diff( molongui_supported_post_types( MOLONGUI_AUTHORSHIP_NAME, 'all', false ), array( 'page', 'post' ) );
            foreach ( $cpts as $cpt ) if ( !in_array( $cpt, $post_types ) ) $post_types[] = $cpt;
            $post_types = array_map( 'trim', $post_types );
            $post_types = array_filter( $post_types );
            $wp_query->set( 'post_type', $post_types );
        }
    }
    add_action( 'pre_get_posts', 'authorship_pro_add_author_cpts', 999 );
}