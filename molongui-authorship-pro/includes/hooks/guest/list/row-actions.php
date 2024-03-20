<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_pro_enable_view_guest_link' ) )
{
    function authorship_pro_enable_view_guest_link()
    {
        $options = authorship_get_options();
        return !$options['guest_pages'];
    }
    add_filter( 'authorship/guest/row_actions/remove_view_link', 'authorship_pro_enable_view_guest_link' );
}
if ( !function_exists( 'authorship_pro_duplicate_guest_link' ) )
{
    function authorship_pro_duplicate_guest_link( $actions, $post )
    {
        if ( $post->post_type === MOLONGUI_AUTHORSHIP_CPT and current_user_can( 'edit_posts' ) and $post->post_status !== 'trash' )
        {
            $actions['duplicate'] = '<a href="admin.php?action=authorship_duplicate_guest&amp;post=' . $post->ID . '" title="' . __( "Duplicate this guest author", 'molongui-authorship-pro' ) . '" rel="permalink">' . __( "Clone", 'molongui-authorship-pro' ) . '</a>';
        }
        return $actions;
    }

    add_filter( 'post_row_actions', 'authorship_pro_duplicate_guest_link', 10, 2 );
    add_action( 'admin_action_authorship_duplicate_guest', 'authorship_clone_post' );
}
if ( !function_exists( 'authorship_pro_convert_guest_link' ) )
{
    function authorship_pro_convert_guest_link( $actions, $post )
    {
        if ( $post->post_type === MOLONGUI_AUTHORSHIP_CPT and current_user_can( 'create_users' ) and $post->post_status !== 'trash' )
        {
            $actions['convert'] = '<a href="admin.php?action=authorship_guest_to_user&amp;post=' . $post->ID . '" title="' . __( "Convert to User", 'molongui-authorship-pro' ) . '" rel="permalink">' . __( "Convert to User", 'molongui-authorship-pro' ) . '</a>';
        }

        return $actions;
    }
    add_filter( 'post_row_actions', 'authorship_pro_convert_guest_link', 10, 2 );
}
if ( !function_exists( 'authorship_pro_convert_guest_to_user' ) )
{
    function authorship_pro_convert_guest_to_user()
    {
        if ( !( isset( $_GET['post'] ) or isset( $_POST['post'] ) or ( isset( $_REQUEST['action'] ) and 'authorship_guest_to_user' == $_REQUEST['action'] ) ) )
        {
            wp_die( __( "No guest to convert has been supplied!", 'molongui-authorship-pro' ) );
        }
        $guest_id = ( isset( $_GET['post'] ) ? $_GET['post'] : $_POST['post'] );
        $author      = new Author( $guest_id, 'guest' );
        $guest_meta  = array_map( function( $a ) use ( $author ) { return $a[0]; }, $author->get_meta( 'all' ) );
        $guest_posts = $author->get_posts( array( 'fields' => 'ids', 'post_type' => 'all' ) );
        $userdata = array
        (
            'user_pass'     => isset( $_GET['pwd'] ) ? $_GET['pwd'] : '',
            'user_login'    => $author->get_slug(),
            'user_email'    => $guest_meta['_molongui_guest_author_mail'],
            'role'          => 'author',
            'user_nicename' => $author->get_slug(),//$guest->post_name,
            'display_name'  => $guest_meta['_molongui_guest_author_display_name'],
            'nickname'      => $guest_meta['_molongui_guest_author_display_name'],
            'first_name'    => $guest_meta['_molongui_guest_author_first_name'],
            'last_name'     => $guest_meta['_molongui_guest_author_last_name'],
            'description'   => $author->get_bio(),
            'user_url'      => $guest_meta['_molongui_guest_author_web'],
        );
        $user_id = wp_insert_user( $userdata );

        if ( is_wp_error( $user_id ) )
        {
            wp_die( $user_id->get_error_message() );
        }
        $img_id = $guest_meta['_thumbnail_id'];
        if ( !empty( $img_id ) )
        {
            update_user_meta( $user_id, 'molongui_author_image_id'  , $img_id );
            update_user_meta( $user_id, 'molongui_author_image_url' , wp_get_attachment_url( $guest_meta['_thumbnail_id'] ) );
            update_user_meta( $user_id, 'molongui_author_image_edit', admin_url( 'post.php?post='.$guest_meta['_thumbnail_id'].'&action=edit&image-editor' ) );
        }
        unset( $guest_meta['_molongui_guest_author_mail'] );
        unset( $guest_meta['_molongui_guest_author_display_name'] );
        unset( $guest_meta['_molongui_guest_author_first_name'] );
        unset( $guest_meta['_molongui_guest_author_last_name'] );
        unset( $guest_meta['_molongui_guest_author_web'] );
        unset( $guest_meta['_thumbnail_id'] );
        foreach ( $guest_meta as $key => $val )
        {
            if ( strpos( $key, '_molongui_guest_' ) === 0 ) update_user_meta( $user_id, str_replace( '_molongui_guest_', 'molongui_', $key ), $val );
        }
        foreach ( $guest_posts as $post_id )
        {
            update_post_meta( $post_id, '_molongui_author', 'user-'.$user_id, 'guest-'.$guest_id );
            if ( get_post_meta( $post_id, '_molongui_main_author', true ) === 'guest-'.$guest_id )
            {
                update_post_meta( $post_id, '_molongui_main_author', 'user-'.$user_id, 'guest-'.$guest_id );
                wp_update_post( array( 'ID' => $post_id, 'post_author' => $user_id ) );
            }
        }
        if ( apply_filters( 'authorship_pro/remove/converted/guest', '__return_true' ) ) wp_delete_post( $guest_id, true );
        wp_redirect( admin_url( 'user-edit.php?user_id=' . $user_id ) );
        exit;
    }
    add_action( 'admin_action_authorship_guest_to_user', 'authorship_pro_convert_guest_to_user' );
}