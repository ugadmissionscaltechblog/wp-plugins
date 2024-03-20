<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_common_tools_options()
{
    $plugin_tools = array();
    $plugin_tools[] = array
    (
        'display' => true,
        'type'    => 'button',
        'class'   => 'is-compact',
        'label'   => __( "Restore plugin configuration from a previous exported backup file", 'molongui-authorship-pro' ),
        'button'  => array
        (
            'display'  => true,
            'type'     => 'input',
            'id'       => 'import_options',
            'label'    => __( "Restore", 'molongui-authorship-pro' ),
            'title'    => __( "Restore Plugin Configuration", 'molongui-authorship-pro' ),
            'class'    => 'm-import-options same-width',
            'disabled' => false,
            'multi'    => false,
            'accept'   => '.json', // Could be multiple extensions: 'image/png, image/jpeg'
        ),
    );
    $plugin_tools[] = array
    (
        'display' => true,
        'type'    => 'button',
        'class'   => 'is-compact',
        'label'   => __( "Reset plugin settings to their defaults", 'molongui-authorship-pro' ),
        'button'  => array
        (
            'display'  => true,
            'type'     => 'action',
            'id'       => 'reset_options',
            'label'    => __( "Reset", 'molongui-authorship-pro' ),
            'title'    => __( "Reset Plugin Configuration", 'molongui-authorship-pro' ),
            'class'    => 'm-reset-options same-width',
            'disabled' => false,
        ),
    );

    return $plugin_tools;
}
add_filter( 'authorship/options/common_tools', 'authorship_pro_common_tools_options' );
function authorship_pro_filter_options_export( $options )
{
    unset( $options[MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_license']    );
    unset( $options[MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_instance']   );
    unset( $options[MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_activated']  );
    unset( $options[MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_product_id'] );

    return $options;
}
add_filter( 'authorship/export_options', 'authorship_pro_filter_options_export' );