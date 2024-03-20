<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_edit_user_scripts( $default )
{
    return apply_filters( 'authorship_pro/edit_user/script', MOLONGUI_AUTHORSHIP_PRO_FOLDER . '/assets/js/edit-user.1f12.min.js', $default );
}
add_filter( 'authorship/edit_user/script', 'authorship_pro_edit_user_scripts' );
function authorship_pro_get_edit_user_params( $params )
{
    $params_pro = array
    (
        1000 => get_current_user_id(),
        1001 => __( "User to Guest Conversion", 'molongui-authorship-pro' ),
        1002 => __( "This user will be removed and a new Guest Author created. Do you want to continue?", 'molongui-authorship-pro' ),
        1003 => __( "Cancel" ),
        1004 => __( "OK" ),
        1005 => __( "Converting...", 'molongui-authorship-pro' ),
        1006 => __( "Can't convert yourself to a guest author. Do it logged in as another user.", 'molongui-authorship-pro' ),
    );
    $params_pro = apply_filters( 'authorship_pro/edit_user/script_params', $params_pro );
    return $params_pro + $params;
}
add_filter( 'authorship/edit_user/script_params', 'authorship_pro_get_edit_user_params' );
add_action( "admin_print_footer_scripts-users.php", 'molongui_enqueue_sweetalert' );