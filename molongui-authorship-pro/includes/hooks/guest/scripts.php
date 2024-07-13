<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_edit_guest_scripts( $default )
{
    return apply_filters( 'authorship_pro/edit_guest/script', MOLONGUI_AUTHORSHIP_PRO_FOLDER . '/assets/js/edit-guest.b4a7.min.js', $default );
}
add_filter( 'authorship/edit_guest/script', 'authorship_pro_edit_guest_scripts' );
function authorship_pro_get_edit_guest_params( $params )
{
    $params_pro = array
    (
        1000 => wp_create_nonce( 'authorship_convert_guest' ),
        1001 => __( "Guest to User Conversion", 'molongui-authorship-pro' ),
        1002 => __( "This guest author will be removed and a new WordPress user with role 'author' created. Do you want to continue?", 'molongui-authorship-pro' ),
        1003 => __( "Cancel" ),
        1004 => __( "OK" ),
        1005 => __( "Provide a password for the new user:", 'molongui-authorship-pro' ),
        1006 => __( "Converting...", 'molongui-authorship-pro' ),
    );
    $params_pro = apply_filters( 'authorship_pro/edit_guest/script_params', $params_pro );
    return $params_pro + $params;
}
add_filter( 'authorship/edit_guest/script_params', 'authorship_pro_get_edit_guest_params' );
add_action( "admin_print_footer_scripts-edit.php", function()
{
    $current_screen = get_current_screen();

    if ( 'edit-'.MOLONGUI_AUTHORSHIP_CPT === $current_screen->id and MOLONGUI_AUTHORSHIP_CPT === $current_screen->post_type )
    {
        molongui_enqueue_sweetalert();
    }
});