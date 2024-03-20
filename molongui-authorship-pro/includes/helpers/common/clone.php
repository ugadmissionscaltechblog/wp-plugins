<?php
defined( 'ABSPATH' ) or exit;
function authorship_clone_post( $post_id = null, $status = null )
{
    $redirect = false;
    if ( empty( $post_id ) )
    {
        $post_id = isset( $_GET['post'] ) ? $_GET['post'] : ( isset( $_POST['post'] ) ? $_POST['post'] : null );

        if ( empty( $post_id ) ) wp_die( "No post to duplicate has been supplied!" );

        $redirect = true;
    }
    $post = get_post( $post_id );
    if ( isset( $post ) and $post != null )
    {
        $current_user    = wp_get_current_user();
        $new_post_author = $current_user->ID;
        $args = array
        (
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name,
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => empty( $status ) ? $post->post_status : $status,
            'post_title'     => $post->post_title,
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order,
        );
        $new_post_id = wp_insert_post( $args );
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $taxonomy )
        {
            $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
            wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
        }
        global $wpdb;
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
        if ( count( $post_meta_infos ) != 0 )
        {
            $sql_query = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value) ";
            foreach ( $post_meta_infos as $meta_info )
            {
                $meta_key = $meta_info->meta_key;
                $meta_value = addslashes($meta_info->meta_value );
                $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query .= implode( " UNION ALL ", $sql_query_sel );
            $wpdb->query( $sql_query );
        }

        if ( $redirect )
        {
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
            exit;
        }
        else
        {
            return true;
        }
    }
    else
    {
        if ( $redirect )
        {
            wp_die( 'Post duplication failed, could not find original post: ' . $post_id );
        }
        else
        {
            return false;
        }
    }
}