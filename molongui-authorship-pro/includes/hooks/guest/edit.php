<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
remove_filter( 'postbox_classes_'.MOLONGUI_AUTHORSHIP_CPT.'_authorshortbiodiv', 'authorship_guest_add_short_bio_metabox_class' );
remove_filter( 'postbox_classes_'.MOLONGUI_AUTHORSHIP_CPT.'_authorconversiondiv', 'authorship_guest_add_conversion_metabox_class' );
function authorship_pro_guest_shortbio_metabox( $post )
{
    return MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/guest-author/html-admin-short-bio-metabox.php';
}
add_filter( 'authorship/admin/guest/shortbio_metabox_html', 'authorship_pro_guest_shortbio_metabox' );
function authorship_pro_guest_conversion_metabox( $post )
{
    return MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/guest-author/html-admin-convert-metabox.php';
}
add_filter( 'authorship/admin/guest/convert_metabox_html', 'authorship_pro_guest_conversion_metabox' );
if ( !function_exists( 'authorship_pro_add_guest_preview_button' ) )
{
    function authorship_pro_add_guest_preview_button( $default, $current_screen )
    {
        global $post;
        if ( $post->post_status == 'publish' and !empty( $post->post_name ) ) return true;

        return $default;
    }
    add_filter( 'authorship/admin/guest/show_preview_button', 'authorship_pro_add_guest_preview_button', 10, 2 );
}
if ( !function_exists( 'authorship_pro_save_guest' ) )
{
    function authorship_pro_save_guest( $id, $post )
    {
        $key = '_molongui_guest_author_short_bio';
        if ( !empty( $post[$key] ) ) update_post_meta( $id, $key, wp_kses_post( $post[$key] ) ); else delete_post_meta( $id, $key );
        $key = '_molongui_guest_author_box_display';
        if ( !empty( $post[$key] ) ) update_post_meta( $id, $key, sanitize_text_field( $post[$key] ) ); else delete_post_meta( $id, $key );
    }
    add_action( 'authorship/guest/save', 'authorship_pro_save_guest', 10, 2 );
}