<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_is_user_query( $wp_query )
{
    $options = Settings::get();

    if ( empty( $options['user_archive_slug'] ) )
    {
        $user = molongui_get_author_by( 'user_nicename', $wp_query->query_vars['guest-author-name'] );
    }
    else
    {
        $users = authorship_pro_get_sanitized_display_names();
        $key   = array_search( $wp_query->query_vars['guest-author-name'], $users );
        $user  = get_user_by( 'slug', $key );
    }

    return $user;
}
function authorship_pro_restore_author_page_query_vars( &$wp_query, $user )
{
    $wp_query->set( 'author', $user->ID );                  // Set author ID.
    $wp_query->set( 'author_name', $user->user_nicename );  // Re-set 'author_name' query_var.
    $wp_query->query['author_name'] = $user->user_nicename; // Re-set 'author_name' query string.
    unset( $wp_query->query['guest-author-name'] );         // Remove 'guest-author-name' query string.
    unset( $wp_query->query_vars['guest-author-name'] );    // Remove 'guest-author-name' query_var.
}
function authorship_pro_set_guest_page_query_vars( &$wp_query, $guest )
{
    authorship_add_author_meta_query( $wp_query, 'guest', $guest->ID );
    $wp_query->guest_author_id = $guest->ID;
    $wp_query->is_guest_author = true;
    $wp_query->set( 'author', 0 );

    $options = Settings::get();
    $cpts    = array();
    if ( isset( $options['guest_archive_include_pages'] ) and $options['guest_archive_include_pages'] ) $cpts[] = 'page';
    if ( isset( $options['guest_archive_include_cpts'] ) and $options['guest_archive_include_cpts'] )
    {
        $cpts = array_merge( $cpts, array_diff( molongui_supported_post_types( MOLONGUI_AUTHORSHIP_NAME, 'all', false ), array( 'page', 'post' ) ) );
    }
    if ( !empty( $cpts ) )
    {
        $post_types = $wp_query->get( 'post_type' );
        if ( !is_array( $post_types ) and !empty( $post_types ) ) $post_types = explode( ',', $post_types );
        if ( empty( $post_types ) )
        {
            $post_types = array();
            $post_types[] = 'post';
        }
        foreach ( $cpts as $cpt ) if ( !in_array( $cpt, $post_types ) ) $post_types[] = $cpt;
        $post_types = array_map( 'trim', $post_types );
        $post_types = array_filter( $post_types );
        $wp_query->set( 'post_type', $post_types );
    }
}