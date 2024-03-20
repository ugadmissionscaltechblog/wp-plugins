<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_options_scripts( $default )
{
    return apply_filters( 'authorship_pro/options/script', MOLONGUI_AUTHORSHIP_PRO_FOLDER . '/assets/js/options.65be.min.js', $default );
}
add_filter( 'authorship/options/script', 'authorship_pro_options_scripts' );
function authorship_pro_options_script_params( $params )
{
    $params_pro = array
    (
        'is_premium' => true,
        1000 => wp_create_nonce( 'authorship_import_guests_nonce' ),
        1001 => __( 'File upload failed', 'molongui-common-framework' ),
        1002 => __( 'Failed to load file.', 'molongui-common-framework' ),
        1003 => __( 'Wrong file type', 'molongui-common-framework' ),
        1004 => __( 'Only valid .JSON files are accepted.', 'molongui-common-framework' ),
        1005 => __( 'Warning', 'molongui-common-framework' ),
        1006 => __( 'You are about to import guest authors. If imported authors already exist in this site, they will be duplicated! Please confirm this action.', 'molongui-authorship-pro' ),
        1007 => __( "Cancel", 'molongui-common-framework' ),
        1008 => __( "OK", 'molongui-common-framework' ),
        1009 => __( "Success!", 'molongui-common-framework' ),
        1010 => __( "Guest authors have been imported successfully.", 'molongui-authorship-pro' ),
        1011 => __( "Error", 'molongui-common-framework' ),
        1012 => __( "Uploaded file is either wrong or empty. Please provide a valid file with data to import.", 'molongui-authorship-pro' ),
        1013 => __( "Something went wrong and some guest authors couldn't be restored. Please make sure uploaded file has content and try uploading the file again.", 'molongui-authorship-pro' ),
        1014 => __( "Something went wrong and couldn't connect to the server. Please, try again.", 'molongui-common-framework' ),
        2000 => wp_create_nonce( 'authorship_remove_guests_nonce' ),
        2001 => __( 'Warning', 'molongui-common-framework' ),
        2002 => __( 'You are about to delete all guest authors in your site. All guest data will be lost! Please confirm this action.', 'molongui-authorship-pro' ),
        2003 => __( "Cancel", 'molongui-common-framework' ),
        2004 => __( "OK", 'molongui-common-framework' ),
        2005 => __( 'Delete', 'molongui-common-framework' ),
        2006 => __( "Guest Authors deleted successfully.", 'molongui-authorship-pro' ),
        2007 => __( "Error", 'molongui-common-framework' ),
        2008 => __( "Something went wrong and guest authors couldn't be deleted correctly. Please, try again.", 'molongui-authorship-pro' ),
        2009 => __( "Something went wrong and couldn't connect to the server. Please, try again.", 'molongui-common-framework' ),
        3000 => wp_create_nonce( 'authorship_import_authorship_nonce' ),
        3001 => __( 'File upload failed', 'molongui-common-framework' ),
        3002 => __( 'Failed to load file.', 'molongui-common-framework' ),
        3003 => __( 'Wrong file type', 'molongui-common-framework' ),
        3004 => __( 'Only valid .JSON files are accepted.', 'molongui-common-framework' ),
        3005 => __( 'Warning', 'molongui-common-framework' ),
        3006 => __( 'You are about to import posts authorship. This only makes sense if you are importing data previously exported from this very same site! Please confirm this action.', 'molongui-authorship-pro' ),
        3007 => __( "Cancel", 'molongui-common-framework' ),
        3008 => __( "OK", 'molongui-common-framework' ),
        3009 => __( "Success!", 'molongui-common-framework' ),
        3010 => __( "Post authorship imported successfully.", 'molongui-authorship-pro' ),
        3011 => __( "Error", 'molongui-common-framework' ),
        3012 => __( "Uploaded file is either wrong or empty. Please provide a valid file with data to import.", 'molongui-authorship-pro' ),
        3013 => __( "Something went wrong and some authorship couldn't be restored. Please make sure uploaded file has content and try uploading the file again.", 'molongui-authorship-pro' ),
        3014 => __( "Something went wrong and couldn't connect to the server. Please, try again.", 'molongui-common-framework' ),
        4000 => wp_create_nonce( 'authorship_remove_authorship_nonce' ),
        4001 => __( 'Warning', 'molongui-common-framework' ),
        4002 => __( 'You are about to delete all posts authorship. Data will be lost! Please confirm this action.', 'molongui-authorship-pro' ),
        4003 => __( "Cancel", 'molongui-common-framework' ),
        4004 => __( "OK", 'molongui-common-framework' ),
        4005 => __( 'Delete', 'molongui-common-framework' ),
        4006 => __( "Posts authorship deleted successfully.", 'molongui-authorship-pro' ),
        4007 => __( "Error", 'molongui-common-framework' ),
        4008 => __( "Something went wrong and authorship couldn't be deleted correctly. Please, try again.", 'molongui-authorship-pro' ),
        4009 => __( "Something went wrong and couldn't connect to the server. Please, try again.", 'molongui-common-framework' ),
    );
    $params_pro = apply_filters( 'authorship_pro/options/script_params', $params_pro );
    return $params_pro + $params;
}
add_filter( 'authorship/options/script_params', 'authorship_pro_options_script_params' );