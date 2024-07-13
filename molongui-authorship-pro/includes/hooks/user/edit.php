<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_user_bio_tmpl' ) )
{
    function authorship_pro_user_bio_tmpl( $default )
    {
        return MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/user/html-admin-profile-bio.php';
    }
    add_filter( 'authorship/edit/user/bio/tmpl', 'authorship_pro_user_bio_tmpl' );
}
if ( !function_exists( 'authorship_pro_user_tools_tmpl' ) )
{
    function authorship_pro_user_tools_tmpl( $default )
    {
        molongui_enqueue_sweetalert();

        return MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/user/html-admin-profile-tools.php';
    }
    add_filter( 'authorship/edit/user/tools/tmpl', 'authorship_pro_user_tools_tmpl' );
}
remove_filter( 'pre_user_description', 'wp_filter_kses' );
if ( !function_exists( 'authorship_pro_save_user' ) )
{
    function authorship_pro_save_user( $id, $user )
    {
        $key = 'molongui_author_box_display';
        if ( empty( $user[$key] ) ) delete_user_meta( $id, $key ); else update_user_meta( $id, $key, sanitize_text_field( $user[$key] ) );
        $key = 'molongui_author_short_bio';
        if ( empty( $user[$key] ) ) delete_user_meta( $id, $key ); else update_user_meta( $id, $key, wp_kses_post( $user[$key] ) );
        $key = 'molongui_author_long_bio';
        if ( isset( $user[$key] ) ) update_user_meta( $id, 'description', wp_kses_post( $user[$key] ) );
    }
    add_action( 'authorship/user/save', 'authorship_pro_save_user', 10, 2 );
}

