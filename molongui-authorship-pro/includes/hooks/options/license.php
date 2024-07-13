<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_deactivated_key_130( $fw_options )
{
    if ( did_action( 'authorship_pro/init' ) or !get_transient( MOLONGUI_AUTHORSHIP_PRO_PREFIX . '_deactivated_key_130' ) ) return $fw_options;

    $fw_options[] = array
    (
        'display' => true,
        'deps'    => '',
        'search'  => '',
        'type'    => 'banner',
        'class'   => 'm-banner-red',
        'default' => '',
        'id'      => 'license_notice',
        'badge'   => __( "TIP", 'molongui-authorship-pro' ),
        'title'   => __( "You need to re-activate your license using your new key.", 'molongui-authorship-pro' ),
        'desc'    => __( "We have e-mailed you your new key. But you can find it also on your My Account", 'molongui-authorship-pro' ),
        'label'   => '',
        'button'  => array
        (
            'label'  => __( "What's my new key?", 'molongui-authorship-pro' ),
            'title'  => __( "Click to find out what your new key is", 'molongui-authorship-pro' ),
            'class'  => 'm-license',
            'href'   => 'https://www.molongui.com/my-account/api-keys/',
            'target' => '_blank',
        ),
    );

    return $fw_options;
}
add_filter( '_authorship_pro/options/license/notice', 'authorship_pro_deactivated_key_130' );