<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_maybe_update_rewrite_rules' ) )
{
    function authorship_pro_maybe_update_rewrite_rules()
    {
        $options = Settings::get();
        if ( empty( $options['guest_authors'] ) or empty( $options['guest_pages'] ) )
        {
            return;
        }

        if ( get_option( MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_flush_rewrite_rules', false ) )
        {
            update_option( MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_flush_rewrite_rules', 0, true );
            flush_rewrite_rules();
        }
    }
    add_action( 'init', 'authorship_pro_maybe_update_rewrite_rules' );
}
if ( !function_exists( 'authorship_pro_update_guest_rewrite_rules' ) )
{
    function authorship_pro_update_guest_rewrite_rules( $author_rewrite )
    {
        $options = Settings::get();
        if ( empty( $options['guest_authors'] ) or empty( $options['guest_pages'] ) )
        {
            return $author_rewrite;
        }

        global $wp_rewrite;

        $author_base  = $wp_rewrite->author_base;
        $guest_struct = empty( $options['guest_archive_permalink'] ) ? '' : $options['guest_archive_permalink'].'/';
        $guest_base   = empty( $options['guest_archive_base'] ) ? 'author' : $options['guest_archive_base'];

        $guest = array();

        foreach ( $author_rewrite as $endpoint => $query )
        {
            $key   = str_replace( $author_base, $guest_struct.$guest_base, $endpoint );
            $value = str_replace( 'author_name', 'guest-author-name', $query );

            $guest[$key] = $value;
        }

        return array_merge( $author_rewrite, $guest );
    }
    add_action( 'author_rewrite_rules', 'authorship_pro_update_guest_rewrite_rules' );
}
if ( !function_exists( 'authorship_pro_guest_page_query_var' ) )
{
    function authorship_pro_guest_page_query_var( $query_vars )
    {
        if ( is_admin() )
        {
            return $query_vars;
        }

        $query_vars[] = 'guest-author-name';

        return $query_vars;
    }
    add_action( 'query_vars', 'authorship_pro_guest_page_query_var' );
}