<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_export_guests' ) )
{
    function authorship_pro_export_guests()
    {
        $args  = array( 'post_type' => 'guest_author', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'title', 'post_status' => array( 'publish', 'pending', 'draft', 'future' ), 'fields' => 'all' );
        $posts = get_posts( $args );
        foreach ( $posts as $key => $post )
        {
            $postmeta              = get_post_meta( $post->ID );
            $posts[$key]->postmeta = array_combine( array_keys( $postmeta ), array_column( $postmeta, '0' ) );
        }
        echo json_encode( $posts );
        wp_die();
    }
    add_action( 'wp_ajax_authorship_export_guests', 'authorship_pro_export_guests' );
}
if ( !function_exists( 'authorship_pro_import_guests' ) )
{
    function authorship_pro_import_guests()
    {
        check_ajax_referer( 'authorship_import_guests_nonce', 'nonce', true );
        $rc   = 0;
        $data = json_decode( wp_unslash( $_POST['file'] ), true );
        if ( !empty( $data ) )
        {
            foreach ( $data as $post )
            {
                $postarr = array
                (
                    'ID'                    => 0,
                    'post_author'           => get_current_user_id(),
                    'post_date'             => $post['post_date'],
                    'post_date_gmt'         => $post['post_date_gmt'],
                    'post_content'          => $post['post_content'] ? $post['post_content'] : '',
                    'post_title'            => $post['post_title'] ? $post['post_title'] : '',
                    'post_excerpt'          => $post['post_excerpt'] ? $post['post_excerpt'] : '',
                    'post_status'           => $post['post_status'] ? $post['post_status'] : '',
                    'comment_status'        => $post['comment_status'] ? $post['comment_status'] : '',
                    'ping_status'           => $post['ping_status'] ? $post['ping_status'] : '',
                    'post_password'         => $post['post_password'] ? $post['post_password'] : '',
                    'post_name'             => $post['post_name'],
                    'to_ping'               => $post['to_ping'] ? $post['to_ping'] : '',
                    'pinged'                => $post['pinged'] ? $post['pinged'] : '',
                    'post_modified'         => $post['post_modified'],
                    'post_modified_gmt'     => $post['post_modified_gmt'],
                    'post_content_filtered' => $post['post_content_filtered'] ? $post['post_content_filtered '] : '',
                    'post_parent'           => $post['post_parent'],
                    'menu_order'            => $post['menu_order'],
                    'post_type'             => MOLONGUI_AUTHORSHIP_CPT,
                    'post_mime_type'        => $post['post_mime_type'],
                    'meta_input'            => $post['postmeta'],
                );
                $guest_id = wp_insert_post( $postarr, true );
                if ( is_wp_error( $guest_id ) )
                {
                    $rc++;
                }
            }
        }
        else
        {
            $rc = 'empty';
        }
        echo $rc;
        wp_die();
    }
    add_action( 'wp_ajax_authorship_import_guests', 'authorship_pro_import_guests' );
}
if ( !function_exists( 'authorship_pro_remove_guests' ) )
{
    function authorship_pro_remove_guests()
    {
        check_ajax_referer( 'authorship_remove_guests_nonce', 'nonce', true );

        global $wpdb;
        $result = $wpdb->query(
            $wpdb->prepare( "
                DELETE posts,pt,pm
                FROM {$wpdb->prefix}posts posts
                LEFT JOIN {$wpdb->prefix}term_relationships pt ON pt.object_id = posts.ID
                LEFT JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = posts.ID
                WHERE posts.post_type = %s
                ",
                MOLONGUI_AUTHORSHIP_CPT
            )
        );
        $r = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('_molongui_main_author','_molongui_author') AND meta_value LIKE 'guest-%'" ) );
        echo $result;
        wp_die();
    }
    add_action( 'wp_ajax_authorship_remove_guests', 'authorship_pro_remove_guests' );
}