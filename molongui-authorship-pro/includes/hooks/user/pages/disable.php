<?php
defined( 'ABSPATH' ) or exit;
if ( !molongui_is_request( 'frontend' ) ) return;
if ( !isset( $options['user_archive_enabled'] ) ) $options = authorship_get_options();
if ( !$options['user_archive_enabled'] )
{
    add_action( 'template_redirect', function()
    {
        if ( is_guest_author() ) return;
        if ( is_404() and !( get_query_var( 'author' ) or get_query_var( 'author_name' ) ) ) return;

        $options = authorship_get_options();
        $existing_author_page     = is_author();
        $non_existing_author_page = ( is_404() and ( get_query_var( 'author' ) or get_query_var( 'author_name' ) ) );

        if ( ( $existing_author_page or $non_existing_author_page ) and !$options['user_archive_enabled'] )
        {
            $admin_only = false;//$options['user_archive_disable_admins'];
            $author_can = false;

            if ( !is_404() and $admin_only )
            {
                global $post;
                if ( is_object( $post ) ) $author_can = author_can( get_the_ID(), 'administrator' );
            }
            if ( ( $admin_only and $author_can === true ) or ( !$admin_only and !is_404() ) or ( is_404() ) )
            {
                $status = $options['user_archive_status'];
                $url    = $options['user_archive_redirect'];

                if ( $url == '' ) $url = authorship_home_url();
                else $url = get_permalink( $url );

                wp_redirect( $url, $status );
                exit;
            }
        }
    });
    add_filter( 'author_link', function() { return '#molongui-disabled-link'; }, 99 );
    add_filter( 'the_author_posts_link', '__return_empty_string', 99 );
}