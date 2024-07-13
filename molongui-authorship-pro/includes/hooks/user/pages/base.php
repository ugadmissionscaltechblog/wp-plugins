<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_change_author_base' ) )
{
    function authorship_pro_change_author_base()
    {
        $options = Settings::get();
        if ( empty( $options['user_archive_base'] ) ) return;
        global $wp_rewrite;
        $wp_rewrite->author_base = $options['user_archive_base'];
        if ( get_option( MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_flush_rewrite_rules', false ) )
        {
            update_option( MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_flush_rewrite_rules', 0, true );
            flush_rewrite_rules();
        }
    }
    add_action( 'init', 'authorship_pro_change_author_base' );
}
$options = Settings::get();
if ( !empty( $options['user_archive_slug'] ) )
{
    if ( !function_exists( 'authorship_pro_switch_user_archive_slug' ) )
    {
        function authorship_pro_switch_user_archive_slug( $wp_query )
        {
            if ( !is_author() ) return;
            if ( isset( $wp_query->guest_author_id ) ) return;
            if ( !$wp_query->is_main_query() ) return;

            $author_name = get_query_var( 'author_name' );
            $users       = authorship_pro_get_sanitized_display_names();
            $key         = array_search( sanitize_title( $author_name ), $users );
            if ( $key )
            {
                $wp_query->set( 'author_name', $key );

                add_filter( 'authorship/pre_get_user_by', 'authorship_void_filter', 10, 2 );
                $author = get_user_by( 'login', $key );
                remove_filter( 'authorship/pre_get_user_by', 'authorship_void_filter', 10 );

                if ( is_object( $author ) and isset( $author->ID ) )
                {
                    $wp_query->set( 'author', $author->ID );
                }
            }/*
            else
            {
                $wp_query->set( 'author_name', false );
                $wp_query->set( 'author', false );
                add_action( 'template_redirect', array( &$this, 'redirect_404' ) );
            }*/
        }
        add_action( 'pre_get_posts', 'authorship_pro_switch_user_archive_slug', 997 );
    }
    if ( !function_exists( 'authorship_pro_filter_user_archive_slug' ) )
    {
        function authorship_pro_filter_user_archive_slug( $link, $author_id, $author_nicename )
        {
            $users = authorship_pro_get_sanitized_display_names();
            if ( array_key_exists( $author_nicename, $users ) )
            {
                $position = strrpos( $link, $author_nicename );
                if ( false !== $position )
                {
                    $link = substr_replace( $link, $users[$author_nicename], $position, strlen( $author_nicename ) );
                }
            }

            return $link;
        }
        add_filter( 'author_link', 'authorship_pro_filter_user_archive_slug', 10, 3 );
    }
}