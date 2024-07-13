<?php

namespace Molongui\Authorship\Pro\Common\Modules;

use Molongui\Authorship\Common\Utils\Plugin;
use Molongui\Authorship\Common\Modules\Settings as BaseSettings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Settings
{
    public function __construct()
    {
        add_filter( 'authorship/options/common_tools', array( $this, 'common_tools' ) );
        add_filter( 'authorship/export_options', array( $this, 'filter_export' ) );
        add_action( 'wp_ajax_'.MOLONGUI_AUTHORSHIP_PREFIX.'_import_options', array( $this, 'import' ) );
        add_action( 'wp_ajax_'.MOLONGUI_AUTHORSHIP_PREFIX.'_reset_options' , array( $this, 'reset'  ) );
        add_filter( 'authorship/options/common'  , array( __CLASS__, 'get_license_markup' ) );
        add_filter( 'authorship/validate_options', array( $this, 'unset_license' ) );
    }
    public function common_tools()
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
    public function filter_export( $options )
    {
        unset( $options[MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_license']    );
        unset( $options[MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_instance']   );
        unset( $options[MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_activated']  );
        unset( $options[MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_product_id'] );

        return $options;
    }
    function reset()
    {
        $rc = true;
        $defaults = BaseSettings::get_defaults();

        $r = update_option( MOLONGUI_AUTHORSHIP_PREFIX.'_options', $defaults );
        if ( !$r )
        {
            if ( $defaults !== get_option( MOLONGUI_AUTHORSHIP_PREFIX.'_options' ) and $defaults !== maybe_serialize( get_option( MOLONGUI_AUTHORSHIP_PREFIX.'_options' ) ) )
            {
                $rc = 'update';
            }
        }

        return $rc;
    }
    public function ajax_reset()
    {
        check_ajax_referer( 'mfw_reset_options_nonce', 'nonce', true );

        $plugin_id = $_POST['id'];
        $rc = self::reset();
        echo $rc;
        wp_die();
    }
    public function ajax_import()
    {
        check_ajax_referer( 'mfw_import_options_nonce', 'nonce', true );

        $rc             = false;
        $plugin_id      = $_POST['id'];
        $plugin_version = $_POST['version'];
        $options        = json_decode( wp_unslash( $_POST['file'] ), true );
        $prefix         = 'molongui_'.str_replace( '-', '_', $plugin_id ).'_';
        if ( isset( $options ) )
        {
            if ( !empty( $options['plugin_id'] ) and $options['plugin_id'] == $plugin_id and
                !empty( $options['plugin_version'] ) and version_compare( $options['plugin_version'], $plugin_version, '<=' ) )
            {
                unset( $options['plugin_id'] );
                unset( $options['plugin_version'] );
                foreach ( $options as $option => $value )
                {

                    if ( MOLONGUI_AUTHORSHIP_PREFIX.'_options' === $option )
                    {
                        $value = array_merge( Settings::get_defaults(), $value );
                    }
                    $r = update_option( $option, maybe_unserialize( $value ) );
                    if ( !$r )
                    {
                        if ( $value !== get_option( $option ) and $value !== maybe_serialize( get_option( $option ) ) )
                        {
                            $rc = 'update';
                        }
                    }
                }
            }
            else
            {
                $rc = 'plugin';
            }
        }
        else
        {
            $rc = 'file';
        }
        echo $rc;
        wp_die();
    }
    public static function restart()
    {
        check_ajax_referer( 'mfw_reset_options_nonce', 'nonce', true );
        $r = Plugin::restart( $_POST['id'] );
        wp_die();
    }
    public static function get_license_markup( $fw_options )
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
                'desc'    => sprintf( esc_html__( "Please provide your license key and PIN to activate the plugin. You can find them on your %sMy Account%s", 'molongui-authorship-pro' ), '<a href="https://www.molongui.com/my-account/" target="_blank">', '</a>' ),
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
                'text'    => sprintf( esc_html__( "%sThe license key you got by e-mail upon purchase.%s %sYou can also find it in your My Account.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
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
                'text'    => sprintf( esc_html__( "%sThe PIN you got by e-mail upon purchase.%s %sYou can also find it in your My Account.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
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
                'href'    => 'https://www.molongui.com/help/license-activation/',
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
            'help'    => sprintf( esc_html__( '%sWhether to deactivate the license key upon plugin deactivation.%s %sRegardless of this setting, the license will be released when uninstalling the plugin.%s', 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'label'   => __( "Keep plugin license active upon plugin deactivation.", 'molongui-authorship-pro' ),
        );

        return $fw_options;
    }
    public function unset_license( $options )
    {
        unset( $options['key'] );
        unset( $options['product_id'] );
        unset( $options['keep_license'] );

        return $options;
    }

} // class