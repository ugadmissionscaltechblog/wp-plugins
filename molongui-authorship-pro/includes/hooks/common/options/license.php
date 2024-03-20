<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_options_license_markup( $fw_options )
{
    $license_on   = did_action( 'authorship_pro/init' );
    $license_data = (array) get_option( MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_license', array() );

    $fw_options[] = array
    (
        'display' => true,
        'type'    => 'section',
        'id'      => apply_filters( 'authorship_pro/options/license/db_key', MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_license' ),
        'name'    => __( "License", 'molongui-authorship-pro' ),
    );

    /*!
     * PRIVATE FILTER HOOK.
     *
     * For internal use only. Not intended to be used by plugin or theme developers.
     * Future compatibility NOT guaranteed.
     *
     * Please do not rely on this hook for your custom code to work. As a private hook it is meant to be used only by
     * Molongui. It may be edited, renamed or removed from future releases without prior notice or deprecation phase.
     *
     * If you choose to ignore this notice and use this filter, please note that you do so at on your own risk and
     * knowing that it could cause code failure.
     */
    $fw_options = apply_filters( '_authorship_pro/options/license/notice', $fw_options );
    $fw_options[] = array
    (
        'display' => true,
        'type'    => 'header',
        'label'   => __( "License", 'molongui-authorship-pro' ),
        'buttons' => array(),
    );
    if ( $license_on )
    {
        $fw_options[] = array
        (
            'display' => true,
            'type'    => 'notice',
            'class'   => '',
            'default' => '',
            'id'      => 'license_notice',
            'title'   => '',
            'desc'    => __( "Your license is active. You might want to deactivate your license key on this site to use it in different installation. Should you want to re-activate the plugin here, you will need to input your credentials again.", 'molongui-authorship-pro' ),
            'help'    => '',
            'link'    => '',
        );
    }
    else
    {
        $fw_options[] = array
        (
            'display' => true,
            'type'    => 'notice',
            'class'   => '',
            'default' => '',
            'id'      => 'license_notice',
            'title'   => '',
            'desc'    => sprintf( __( "Please provide your license key and PIN to activate the plugin. You can find them on your %sMy Account%s", 'molongui-authorship-pro' ), '<a href="https://www.molongui.com/my-account/" target="_blank">', '</a>' ),
            'help'    => '',
            'link'    => '',
        );
    }
    $fw_options[] = array
    (
        'display'     => true,
        'type'        => 'text',
        'id'          => 'key',
        'placeholder' => __( "Type here your license key", 'molongui-authorship-pro' ),
        'default'     => empty( $license_data['key'] ) ? '' : $license_data['key'],
        'class'       => $license_on ? ' m-license-on' : '',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sThe license key you got by e-mail upon purchase.%s %sYou can also find it in your My Account.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'link'    => array
            (
                'label'  => 'My Account',
                'target' => 'external',
                'url'    => 'https://www.molongui.com/my-account/',
            ),
        ),
        'label'       => __( "License KEY", 'molongui-authorship-pro' ),
    );
    $fw_options[] = array
    (
        'display'     => true,
        'type'        => 'text',
        'id'          => 'product_id',
        'placeholder' => __( "Type here your license PIN", 'molongui-authorship-pro' ),
        'default'     => empty( $license_data['product_id'] ) ? '' : $license_data['product_id'],
        'class'       => $license_on ? ' m-license-on' : '',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sThe PIN you got by e-mail upon purchase.%s %sYou can also find it in your My Account.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'link'    => array
            (
                'label'  => 'My Account',
                'target' => 'external',
                'url'    => 'https://www.molongui.com/my-account/',
            ),
        ),
        'label'       => __( "License PIN", 'molongui-authorship-pro' ),
    );
    if ( $license_on )
    {
        $fw_options[] = array
        (
            'display' => true,
            'type'    => 'header',
            'class'   => 'is-compact',
            'label'   => __( "Deactivating the license will make the plugin to stop working on this site.", 'molongui-authorship-pro' ),
            'buttons' => array
            (
                'deactivate' => array
                (
                    'display'  => true,
                    'type'     => 'action',
                    'id'       => 'deactivate_license_button',
                    'label'    => __( "Deactivate", 'molongui-authorship-pro' ),
                    'title'    => __( "Deactivate License", 'molongui-authorship-pro' ),
                    'class'    => 'm-license m-deactivate same-width',
                    'disabled' => false,
                ),
            ),
        );
    }
    else
    {
        $fw_options[] = array
        (
            'display' => false,
            'type'    => 'link',
            'class'   => '',
            'default' => '',
            'id'      => '',
            'title'   => '',
            'desc'    => '',
            'help'    => __( "Click here to get some help", 'molongui-authorship-pro' ),
            'label'   => __( "Can't activate your license? Get help", 'molongui-authorship-pro' ),
            'href'    => 'https://www.molongui.com/help/docs/'.MOLONGUI_AUTHORSHIP_TITLE.'/license-activation/',
            'target'  => '_blank',
        );
        $fw_options[] = array
        (
            'display' => true,
            'type'    => 'header',
            'class'   => 'is-compact',
            'label'   => '',
            'buttons' => array
            (
                'activate' => array
                (
                    'display'  => true,
                    'type'     => 'action',
                    'id'       => 'activate_license_button',
                    'label'    => __( "Activate", 'molongui-authorship-pro' ),
                    'title'    => __( "Activate License", 'molongui-authorship-pro' ),
                    'class'    => 'is-primary m-license m-activate same-width',
                    'disabled' => false,
                ),
            ),
        );
    }
    $fw_options[] = array
    (
        'display' => true,
        'type'    => 'header',
        'label'   => __( "Retention", 'molongui-authorship-pro' ),
        'buttons' => array(),
    );
    $fw_options[] = array
    (
        'display' => true,
        'type'    => 'toggle',
        'class'   => '',
        'default' => true,
        'id'      => 'keep_license',
        'title'   => '',
        'desc'    => '',
        'help'    => sprintf( __( '%sWhether to deactivate the license key upon plugin deactivation.%s %sRegardless of this setting, the license will be released when uninstalling the plugin.%s', 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
        'label'   => __( "Keep plugin license active upon plugin deactivation.", 'molongui-authorship-pro' ),
    );

    return $fw_options;
}
add_filter( 'authorship/options/common', 'authorship_pro_options_license_markup' );
function authorship_pro_unset_license_options( $options )
{
    unset( $options['key'] );
    unset( $options['product_id'] );
    unset( $options['keep_license'] );

    return $options;
}
add_filter( 'authorship/validate_options', 'authorship_pro_unset_license_options' );