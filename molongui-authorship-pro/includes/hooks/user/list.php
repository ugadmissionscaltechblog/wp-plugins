<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_pro_user_convert_link' ) )
{
    function authorship_pro_user_convert_link( $actions, $user )
    {
        if ( authorship_is_feature_enabled( 'guest' ) )
        {
            $actions['convert'] = '<a href="admin.php?action=authorship_user_to_guest&amp;user=' . $user->ID . '" data-user-id="' . $user->ID . '" title="' . __( "Convert to Guest", 'molongui-authorship-pro' ) . '" rel="permalink">' . __( 'Convert to Guest', 'molongui-authorship-pro' ) . '</a>';
        }

        return $actions;
    }
    add_filter( 'user_row_actions','authorship_pro_user_convert_link', 10, 2 );
}
if ( !function_exists( 'authorship_pro_user_convert_to_guest' ) )
{
    function authorship_pro_user_convert_to_guest()
    {
        if ( !authorship_is_feature_enabled( 'guest' ) )
        {
            wp_die( __( "The 'Guest Authors' feature is disabled! Enable it in the plugin settings page in order to proceed.", 'molongui-authorship-pro' ) );
        }

        if ( !( isset( $_GET['user'] ) or isset( $_POST['user'] ) or ( isset( $_REQUEST['action'] ) and 'authorship_user_to_guest' == $_REQUEST['action'] ) ) )
        {
            wp_die( __( "No user to convert has been supplied!", 'molongui-authorship-pro' ) );
        }
        $user_id = ( isset( $_GET['user'] ) ? $_GET['user'] : $_POST['user'] );
        $author    = new Author( $user_id, 'user' );
        $user      = $author->get();
        $user_meta = array_map( function( $a )
        {
            return $a[0];
        }, get_user_meta( $user_id ) );
        add_filter( 'authorship/author/get_posts', 'authorship_pro_get_all_user_posts', 10, 5 );
        $user_posts = $author->get_posts( array( 'fields' => 'ids', 'post_type' => 'all' ) );
        remove_filter( 'authorship/author/get_posts', 'authorship_pro_get_all_user_posts', 10 );
        $postarr = array
        (
            'post_type'      => 'guest_author',
            'post_name'      => $user->user_nicename,
            'post_title'     => $user->display_name,
            'post_excerpt'   => '',
            'post_content'   => $user->description,
            'thumbnail'      => '',
            'meta_input'     => array
            (
                '_molongui_guest_author_display_name' => $user->display_name,
                '_molongui_guest_author_first_name'   => $user->first_name,
                '_molongui_guest_author_last_name'    => $user->last_name,
                '_molongui_guest_author_mail'         => $user->user_email,
                '_molongui_guest_author_web'          => $user->user_url,
            ),
            'post_status'    => 'publish',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_author'    => get_current_user_id(),
        );
        $guest_id = wp_insert_post( $postarr, true );

        if ( is_wp_error( $guest_id ) )
        {
            wp_die( $guest_id->get_error_message() );
        }
        $img_id = get_user_meta( $user_id, 'molongui_author_image_id', true );
        if ( !empty( $img_id ) )
        {
            update_post_meta( $guest_id, '_thumbnail_id', $img_id );
            update_post_meta( $guest_id, '_molongui_guest_author_image_id', $img_id );
            update_post_meta( $guest_id, '_molongui_guest_author_image_url', get_user_meta( $user_id, 'molongui_author_image_url' ) );
            update_post_meta( $guest_id, '_molongui_guest_author_image_edit', get_user_meta( $user_id, 'molongui_author_image_edit' ) );
        }
        unset( $user_meta['molongui_author_image_id'] );
        unset( $user_meta['molongui_author_image_url'] );
        unset( $user_meta['molongui_author_image_edit'] );
        foreach ( $user_meta as $key => $val )
        {
            if ( strpos( $key, 'molongui_author_' ) === 0 ) update_post_meta( $guest_id, str_replace( 'molongui_', '_molongui_guest_', $key ), $val );
        }
        foreach ( $user_posts as $post_id )
        {
            if ( get_post_meta( $post_id, '_molongui_author', true ) )
            {
                update_post_meta( $post_id, '_molongui_author', 'guest-' . $guest_id, 'user-' . $user_id );
                if ( get_post_meta( $post_id, '_molongui_main_author', true ) === 'user-' . $user_id )
                {
                    update_post_meta( $post_id, '_molongui_main_author', 'guest-' . $guest_id, 'user-' . $user_id );
                    $found = false;
                    foreach ( get_post_meta( $post_id, '_molongui_author', false ) as $key => $value )
                    {
                        if ( strpos( $value, 'user-' ) !== false )
                        {
                            $id = str_replace( 'user-', '', $value );
                            if ( $id != $user_id )
                            {
                                wp_update_post( array( 'ID' => $post_id, 'post_author' => $id ) );
                                $found = true;
                                break;
                            }
                        }
                    }
                    if ( !$found ) wp_update_post( array( 'ID' => $post_id, 'post_author' => get_current_user_id() ) );
                }
                else
                {
                    wp_update_post( array( 'ID' => $post_id, 'post_author' => get_current_user_id() ) );
                }
            }
            else
            {
                add_post_meta( $post_id, '_molongui_author', 'guest-' . $guest_id, false );
                add_post_meta( $post_id, '_molongui_main_author', 'guest-' . $guest_id, true );
                wp_update_post( array( 'ID' => $post_id, 'post_author' => get_current_user_id() ) );
            }
        }
        if ( apply_filters( 'authorship_pro/remove/converted/user', '__return_true' ) ) wp_delete_user( $user_id );
        wp_redirect( admin_url( 'post.php?post=' . $guest_id . '&action=edit' ) );
        exit;
    }
    add_action( 'admin_action_authorship_user_to_guest', 'authorship_pro_user_convert_to_guest' );
}