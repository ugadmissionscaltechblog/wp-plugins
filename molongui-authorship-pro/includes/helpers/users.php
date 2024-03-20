<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_pro_get_sanitized_display_names' ) )
{
    function authorship_pro_get_sanitized_display_names()
    {
        $i = 1;
        $args  = array();
        $hash  = md5( serialize( $args ) );
        $key   = 'get_users' . '_' . $hash;
        $users = wp_cache_get( $key, MOLONGUI_AUTHORSHIP_PRO_NAME );
        if ( false === $users )
        {
            $users = array();
            foreach ( get_users() as $user )
            {
                $display_name = sanitize_title( $user->display_name );
                if ( in_array( $display_name, $users ) )
                {
                    $i++;
                    $display_name .= "-$i";
                }
                $users[sanitize_title($user->user_nicename)] = $display_name;
            }
            wp_cache_set( $key, $users, MOLONGUI_AUTHORSHIP_PRO_NAME );
            $db_key = MOLONGUI_AUTHORSHIP_PREFIX . 'cache_users';
            $hashes = get_option( $db_key, array() );
            $update = update_option( $db_key, !in_array( $hash, $hashes ) ? array_merge( $hashes, array( $hash ) ) : $hashes, true );
        }
        return $users;
    }
}
if ( !function_exists( 'authorship_pro_get_all_user_posts' ) )
{
    function authorship_pro_get_all_user_posts( $data, $author_id, $author_type, $author, $parsed_args )
    {
        if ( !empty( $parsed_args['meta_query'] ) )
        {
            $mq = array
            (
                array
                (
                    'key'   => $parsed_args['meta_query']['key'],
                    'value' => $parsed_args['meta_query']['value'],
                ),
            );
        }
        $args1 = array
        (
            'post_type'           => $parsed_args['post_type'],
            'post__in'            => $parsed_args['post__in'],
            'post__not_in'        => $parsed_args['post__not_in'],
            'post_status'         => $parsed_args['post_status'],
            'cat'                 => $parsed_args['cat'],
            'meta_query'          => isset( $mq ) ? $mq : '',
            'author'              => $author_id,
            'orderby'             => $parsed_args['orderby'],
            'order'               => $parsed_args['order'],
            'posts_per_page'      => $parsed_args['posts_per_page'],
            'no_found_rows'       => $parsed_args['no_found_rows'],
            'ignore_sticky_posts' => $parsed_args['ignore_sticky_posts'],
            'fields'              => $parsed_args['fields'],
        );
        $hash  = \md5( \serialize( array_merge( $args1, array( $author_id, $author_type ) ) ) );
        $key   = 'posts' . '_' . $hash;
        $data1 = \wp_cache_get( $key, MOLONGUI_AUTHORSHIP_NAME );

        if ( false === $data1 )
        {
            $data1 = new \WP_Query( $args1 );
            $data1 = $data1->posts;
            \wp_cache_set( $key, $data1, MOLONGUI_AUTHORSHIP_NAME );
            $db_key = MOLONGUI_AUTHORSHIP_PREFIX . 'cache_posts';
            $hashes = \get_option( $db_key, array() );
            $update = \update_option( $db_key, !\in_array( $hash, $hashes ) ? \array_merge( $hashes, array( $hash ) ) : $hashes, true );
        }
        if ( \is_array( $data1 ) ) return \array_merge( $data, $data1 );
        return $data;
    }
}