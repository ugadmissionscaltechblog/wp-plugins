<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_pro_guest_page_filter_posts' ) )
{
    function authorship_pro_guest_page_filter_posts( $wp_query )
    {
        $options = authorship_get_options();
        if ( empty( $options['guest_authors'] ) or empty( $options['guest_pages'] ) ) return;
        if ( ( is_admin() and ( !defined( 'DOING_AJAX' ) or !DOING_AJAX /*or !wp_doing_ajax()*/ ) )
             or ( !$wp_query->is_main_query() and apply_filters_ref_array( 'molongui_edit_main_query_only', array( true, &$wp_query ) ) )
             or !isset( $wp_query->query_vars['guest-author-name'] ) or empty( $wp_query->query_vars['guest-author-name'] )
        ) return;
        remove_action( 'pre_get_posts', 'authorship_pro_guest_page_filter_posts', 998 );
        if ( apply_filters( 'authorship/user_profile_prevails', true ) )
        {
            if ( $user = authorship_pro_is_user_query( $wp_query ) )
            {
                authorship_pro_restore_author_page_query_vars( $wp_query, $user );
            }
            else
            {
                if ( $guest = molongui_get_author_by( 'name', $wp_query->query_vars['guest-author-name'], 'guest', false ) )
                {
                    authorship_pro_set_guest_page_query_vars( $wp_query, $guest );
                }
                else
                {
                    $wp_query->is_404 = true;
                    return;
                }
            }
        }
        else
        {
            if ( $guest = molongui_get_author_by( 'name', $wp_query->query_vars['guest-author-name'], 'guest', false ) )
            {
                authorship_pro_set_guest_page_query_vars( $wp_query, $guest );
            }
            else
            {
                if ( $user = authorship_pro_is_user_query( $wp_query ) )
                {
                    authorship_pro_restore_author_page_query_vars( $wp_query, $user );
                }
                else
                {
                    $wp_query->is_404 = true;
                    return;
                }
            }
        }
        $wp_query->set( 'ignore_sticky_posts', true );
        $wp_query->set( 'post__not_in', array() );
        if ( $wp_query->is_paged )
        {
            $page_offset = ( $wp_query->query_vars['paged'] - 1 ) * get_option( 'posts_per_page' );
            $wp_query->set( 'offset', $page_offset );
        }
        else
        {
            $wp_query->set( 'offset', '0' );
        }
        $wp_query->is_single = false;
        $wp_query->is_preview = false;
        $wp_query->is_page = false;
        $wp_query->is_archive = true;
        $wp_query->is_date = false;
        $wp_query->is_year = false;
        $wp_query->is_month = false;
        $wp_query->is_day = false;
        $wp_query->is_time = false;
        $wp_query->is_author = apply_filters( 'authorship_pro/guest/archive/is_author', true, $wp_query );
        $wp_query->is_category = false;
        $wp_query->is_tag = false;
        $wp_query->is_tax = false;
        $wp_query->is_search = false;
        $wp_query->is_feed = false;
        $wp_query->is_comment_feed = false;
        $wp_query->is_trackback = false;
        $wp_query->is_home = false;
        $wp_query->is_404 = false;
        $wp_query->is_embed = false;
        $wp_query->is_admin = false;
        $wp_query->is_attachment = false;
        $wp_query->is_singular = false;
        $wp_query->is_robots = false;
        $wp_query->is_posts_page = false;
        $wp_query->is_post_type_archive = false;
        add_action( 'pre_get_posts', 'authorship_pro_guest_page_filter_posts', 998 );
    }
    add_action( 'pre_get_posts', 'authorship_pro_guest_page_filter_posts', 998 );
}