<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_export_authorship' ) )
{
    function authorship_pro_export_authorship()
    {
        global $wpdb;
        $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('_molongui_main_author','_molongui_author')", ARRAY_A );
        echo json_encode( $rows );
        wp_die();
    }
    add_action( 'wp_ajax_authorship_export_authorship', 'authorship_pro_export_authorship' );
}
if ( !function_exists( 'authorship_pro_import_authorship' ) )
{
    function authorship_pro_import_authorship()
    {
        check_ajax_referer( 'authorship_import_authorship_nonce', 'nonce', true );
        $rc   = 0;
        $data = json_decode( wp_unslash( $_POST['file'] ), true );
        if ( !empty( $data ) )
        {
            foreach ( $data as $authorship )
            {
                $meta = add_post_meta( $authorship['post_id'], $authorship['meta_key'], $authorship['meta_value'], false );
                if ( !$meta )
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
    add_action( 'wp_ajax_authorship_import_authorship', 'authorship_pro_import_authorship' );
}
if ( !function_exists( 'authorship_pro_remove_authorship' ) )
{
    function authorship_pro_remove_authorship()
    {
        check_ajax_referer( 'authorship_remove_authorship_nonce', 'nonce', true );

        global $wpdb;
        $result = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('_molongui_main_author','_molongui_author')" ) );
        echo $result;
        wp_die();
    }
    add_action( 'wp_ajax_authorship_remove_authorship', 'authorship_pro_remove_authorship' );
}