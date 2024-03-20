<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
if ( !molongui_is_request( 'frontend' ) ) return;
if ( apply_filters( 'authorship_pro/search_by_author_name', true ) and authorship_is_feature_enabled( 'author_search' ) )
{
    add_filter( 'posts_join',    'molongui_filter_join',    10, 2 );
    add_filter( 'posts_where',   'molongui_filter_search',  10, 2 ); //add_filter( 'posts_search',  'molongui_filter_search', 10, 2 );
    add_filter( 'posts_groupby', 'molongui_filter_groupby', 10, 2 );
    add_filter( 'posts_orderby', 'molongui_filter_orderby', 10, 2 );
    function molongui_filter_join( $join, $query )
    {
        if ( is_admin() or !is_search() or !$query->is_main_query() ) return $join;
        $alias = 'molongui_meta';
        global $wpdb;
        $join .= ' LEFT JOIN '.$wpdb->postmeta.' '.$alias.' ON '.$wpdb->posts.'.ID = '.$alias.'.post_id ';

        return $join;
    }
    function molongui_filter_search( $where, $query )
    {
        if ( is_admin() or !is_search() or !$query->is_main_query() /*or empty( $sql_search )*/ ) return $where;
        $search = sanitize_text_field( get_query_var( 's' ) );
        $args = array
        (
            'fields'         => 'ids',
            'search'         => sprintf( '*%s*', $search ),
            'search_columns' => array( 'display_name' ),
            'order'          => 'ASC',
            'orderby'        => 'display_name',
        );
        $users_1 = get_users( $args );
        $args = array
        (
            'fields'         => 'ids',
            'search'         => sprintf( '*%s*', $search ),
            'order'          => 'ASC',
            'orderby'        => 'display_name',
            'meta_query'     => array
              (
                  'relation' => 'OR',
                  array
                  (
                      'key'     => 'first_name',
                      'value'   => $search,
                      'compare' => 'LIKE',
                  ),
                  array
                  (
                      'key'     => 'last_name',
                      'value'   => $search,
                      'compare' => 'LIKE',
                  ),
              ),
        );
        $users_2 = get_users( $args );
        $users = array_unique( array_merge( $users_1, $users_2 ) );
        $authors = $users;
        array_walk( $authors, function( &$value, $key ) { $value = "'user-".$value."'"; } );
        array_walk( $users,   function( &$value, $key ) { $value = "'".$value."'"; } );

        if ( authorship_is_feature_enabled( 'guest' ) )
        {
            $args = array
            (
                'post_type'      => 'guest_author',
                'posts_per_page' => -1,
                'order'          => 'ASC',
                'orderby'        => 'title',
                'post_status'    => 'publish',
                'fields'         => 'ids',
                'meta_query'     => array
                (
                    'relation' => 'OR',
                    array
                    (
                        'key'     => '_molongui_guest_author_first_name',
                        'value'   => $search,
                        'compare' => 'LIKE',
                    ),
                    array
                    (
                        'key'     => '_molongui_guest_author_last_name',
                        'value'   => $search,
                        'compare' => 'LIKE',
                    ),
                    array
                    (
                        'key'     => '_molongui_guest_author_display_name',
                        'value'   => $search,
                        'compare' => 'LIKE',
                    ),
                ),
            );
            $guests_1 = get_posts( $args );
            $args = array
            (
                'post_type'      => 'guest_author',
                'posts_per_page' => -1,
                'order'          => 'ASC',
                'orderby'        => 'title',
                'post_status'    => 'publish',
                'fields'         => 'ids',
                's'              => $search,
            );
            $guests_2 = get_posts( $args );
            $guests = array_unique( array_merge( $guests_1, $guests_2 ) );
            array_walk( $guests, function( &$value, $key ) { $value = '"guest-'.$value.'"'; } );
            $authors = array_merge( $authors, $guests );
        }
        if ( empty( $authors ) ) return $where;
        $post_types = molongui_supported_post_types( MOLONGUI_AUTHORSHIP_NAME, 'all' );
        array_walk( $post_types,   function( &$value, $key ) { $value = "'".$value."'"; } );
        $post_types = implode( ',', $post_types );
        $alias = 'molongui_meta';
        global $wpdb;
        if ( !empty( $users ) )   $where .= " OR ( {$wpdb->posts}.post_author IN (" . implode( ',', $users ) . ") AND {$wpdb->posts}.post_type IN (" . $post_types . ") AND {$wpdb->posts}.post_status = 'publish' )";
        if ( !empty( $authors ) ) $where .= " OR ( {$alias}.meta_key = '_molongui_author' AND {$alias}.meta_value IN (" . implode( ',', $authors ) . ") AND {$wpdb->posts}.post_type IN (" . $post_types . ") AND {$wpdb->posts}.post_status = 'publish' )";
        return $where;
    }
    function molongui_filter_groupby( $groupby, $query )
    {
        if ( is_admin() or !is_search() or !$query->is_main_query() ) return $groupby;
        global $wpdb;
        $groupby = "$wpdb->posts.ID";

        return $groupby;
    }
    function molongui_filter_orderby( $orderby, $query )
    {
        if ( is_admin() or !is_search() or !$query->is_main_query() ) return $orderby;
        global $wpdb;
        $orderby = "{$wpdb->posts}.post_date DESC";

        return $orderby;
    }
}