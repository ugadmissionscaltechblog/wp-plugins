<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_pro_plugin_details' ) )
{
    function authorship_pro_plugin_details()
    {
        if ( apply_filters( 'authorship_pro/add_plugin_sign', true ) )
        {
            echo '<div data-m-brand="Molongui" data-m-id="'.MOLONGUI_AUTHORSHIP_TAG.'" data-m-license="Pro" data-m-version="'.MOLONGUI_AUTHORSHIP_PRO_VERSION.'" data-m-link="'.MOLONGUI_AUTHORSHIP_WEB.'"></div>';
        }
    }
    add_action( 'wp_footer', 'authorship_pro_plugin_details', 999 );
}