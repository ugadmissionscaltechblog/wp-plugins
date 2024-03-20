<?php
defined( 'ABSPATH' ) or exit;
add_action( 'authorship/admin/guest/metaboxes', 'authorship_pro_add_yoast_metabox', 10, 1 );
if ( !function_exists( 'authorship_pro_add_yoast_metabox' ) )
{
    function authorship_pro_add_yoast_metabox( $post_type )
    {
        add_meta_box
        (
            'authorseodiv'
            ,__( 'Yoast SEO Settings', 'molongui-authorship-pro' )
            ,'authorship_pro_render_yoast_metabox'
            ,$post_type
            ,'side'
            ,'low'
        );
    }
}
if ( !function_exists( 'authorship_pro_render_yoast_metabox' ) )
{
    function authorship_pro_render_yoast_metabox( $post )
    {
        wp_nonce_field( 'molongui_authorship', 'molongui_authorship_nonce' );
        $guest_author_noindex = get_post_meta( $post->ID, '_molongui_guest_author_noindex', true );
        include MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/guest-author/html-admin-seo-metabox.php';
    }
}
if ( molongui_is_request( 'admin' ) ) return;
add_filter( 'wpseo_robots'                       , 'authorship_pro_wpseo_robots'               , PHP_INT_MAX , 1 );
add_filter( 'wpseo_replacements'                 , 'authorship_pro_wpseo_replacements'         , PHP_INT_MAX , 2 );
add_filter( 'wpseo_metadesc'                     , 'authorship_pro_wpseo_metadesc'             , PHP_INT_MAX , 1 );
add_filter( 'get_the_author_wpseo_title'         , 'authorship_pro_wpseo_clear_author_meta'    , PHP_INT_MAX , 3 );
add_filter( 'get_the_author_wpseo_metadesc'      , 'authorship_pro_wpseo_clear_author_meta'    , PHP_INT_MAX , 3 );
add_filter( 'get_the_author_wpseo_noindex_author', 'authorship_pro_wpseo_clear_author_meta'    , PHP_INT_MAX , 3 );
add_filter( 'wpseo_should_save_indexable'        , 'authorship_pro_wpseo_should_save_indexable', PHP_INT_MAX , 2 );

add_filter( 'authorship_pro/author/url'          , 'authorship_pro_guest_url_sitemap'          ,  10, 2 );
if ( !function_exists( 'authorship_pro_wpseo_robots' ) )
{
    function authorship_pro_wpseo_robots( $robots_string )
    {
        if ( is_guest_author() )
        {
            global $wp_query;
            $robots           = array();
            $robots['index']  = 'index';
            $robots['follow'] = 'follow';
            if ( class_exists( 'WPSEO_Options' ) )
            {
                if ( WPSEO_Options::get( 'noindex-author-wpseo', false ) )
                {
                    $robots['index']  = 'noindex';
                    $robots['follow'] = 'nofollow';
                }
                elseif ( WPSEO_Options::get( 'noindex-author-noposts-wpseo', false ) )
                {
                    if ( !empty( $wp_query->guest_author_id ) )
                    {
                        global $wpdb;
                        $guest_posts = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_molongui_author' AND meta_value = 'guest-{$wp_query->guest_author_id}'", ARRAY_A );

                        if ( empty( $guest_posts ) )
                        {
                            $robots['index']  = 'noindex';
                            $robots['follow'] = 'nofollow';
                        }
                    }
                }
            }
            if ( '0' === (string) get_option( 'blog_public' ) || isset( $_GET['replytocom'] ) )
            {
                $robots['index']  = 'noindex';
                $robots['follow'] = 'nofollow';
            }
            $search = array( 'noindex, ', 'noindex,', 'noindex', 'index,', 'index', 'nofollow, ', 'nofollow,', 'nofollow', 'follow,', 'follow' );
            $robots_string = trim( trim( str_replace( $search, '', $robots_string ), ',' ) );
            $robots_string = $robots['index'] . ', ' . $robots['follow'] . ( empty( $robots_string ) ? '' : ', ' . $robots_string );
        }

        /*!
         * FILTER HOOK
         *
         * Allows filtering the meta robots output.
         *
         * @param  string  $robots_string  The meta robots directives to be echoed.
         * @since  1.6.3
         */
        return apply_filters( 'authorship_pro/wpseo_robots', $robots_string );
    }
}
if ( !function_exists( 'authorship_pro_wpseo_replacements' ) )
{
    function authorship_pro_wpseo_replacements( $replacements, $args = null )
    {
        if ( !is_guest_author() ) return $replacements;
        if ( !in_the_loop() and isset( $replacements['%%name%%'] ) )
        {
            if ( $author = get_query_var( 'guest-author-name', 0 ) )
            {
                $guest = molongui_get_author_by( 'name', $author, 'guest', false );
                if ( $guest ) $replacements['%%name%%'] = $guest->post_title;
            }
        }
        return $replacements;
    }
}
if ( !function_exists( 'authorship_pro_wpseo_metadesc' ) )
{
    function authorship_pro_wpseo_metadesc( $metadesc )
    {
        if ( !is_guest_author() ) return $metadesc;
        if ( !in_the_loop() )
        {
            if ( $author = get_query_var( 'guest-author-name', 0 ) )
            {
                $guest = molongui_get_author_by( 'name', $author, 'guest', false );
                if ( $guest ) $metadesc = $guest->post_content;
            }
        }
        return $metadesc;
    }
}
if ( !function_exists( 'authorship_pro_wpseo_clear_author_meta' ) )
{
    function authorship_pro_wpseo_clear_author_meta( $value, $user_id = 0, $original_user_id = 0 )
    {
        if ( is_guest_author() and $original_user_id === 0 ) return '';
        return $value;
    }
}
if ( !function_exists( 'authorship_pro_wpseo_should_save_indexable' ) )
{
    function authorship_pro_wpseo_should_save_indexable( $default, $indexable )
    {
        if ( is_guest_author() ) return false;
        return $default;
    }
}
if ( !function_exists( 'authorship_pro_guest_url_sitemap' ) )
{
    function authorship_pro_guest_url_sitemap( $url, $actual_url )
    {
        $i  = 3;
        $fn = 'molongui_generate_guests_sitemap';
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 4 );
        if ( isset( $dbt[$i]['function'] ) and $dbt[$i]['function'] == $fn ) return $actual_url;
        return $url;
    }
}
if ( !function_exists( 'authorship_pro_wpseo_title' ) )
{
    function authorship_pro_wpseo_title( $title, $presentation )
    {
        if ( !is_guest_author() ) return $title;
        if ( !in_the_loop() and $title )
        {
            if ( $author = get_query_var( 'guest-author-name', 0 ) )
            {
                $guest = molongui_get_author_by( 'name', $author, 'guest', false );
                if ( $guest ) $title = $guest->post_title;
            }
        }
        return $title;
    }
}