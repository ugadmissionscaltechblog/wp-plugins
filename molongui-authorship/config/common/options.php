<?php
defined( 'ABSPATH' ) or exit;

$fw_options = array();
if ( apply_filters( 'authorship/options/add_common', true ) )
{
    $fw_options[] = array
    (
        'display' => true,
        'type'    => 'section',
        'id'      => 'advanced',
        'name'    => __( "Advanced", 'molongui-authorship' ),
    );
    $fw_options[] = array
    (
        'display' => true,
        'deps'    => '',
        'search'  => '',
        'type'    => 'header',
        'class'   => '',
        'id'      => 'custom_css_header',
        'label'   => __( "Custom CSS", 'molongui-authorship' ),
        'button'  => array(),
    );
    $fw_options[] = array
    (
        'display'     => true,
        'advanced'    => false,
        'deps'        => '',
        'search'      => '',
        'type'        => 'textarea',
        'class'       => 'codemirror-css',
        'default'     => '',
        'placeholder' => '',
        'id'          => 'custom_css',
        'rows'        => 10,
        'title'       => '',
        'label'       => '',
        'desc'        => __( "Enter here any CSS rules you want to add to your site to modify default plugin styles. If you need help customizing anything, feel free to open a support ticket with us and we will be happy to help.", 'molongui-authorship' ),
        'help'        => array
        (
            'text' => sprintf( __( "%sYou can add any CSS rules you wish.%s%sIf you need help customizing anything, feel free to open a support ticket with us and we will be happy to help.%s", 'molongui-authorship' ), '<p>', '</p>', '<p>', '</p>' ),
            'link' => array
            (
                'label'  => __( "Submit help request", 'molongui-authorship' ),
                'url'    => 'https://www.molongui.com/help/support/',
                'target' => '_blank',
            ),
        ),
    );
    $fw_options[] = array
    (
        'display' => true,
        'deps'    => '',
        'search'  => '',
        'type'    => 'header',
        'class'   => '',
        'id'      => 'custom_php_header',
        'label'   => __( "Custom PHP", 'molongui-authorship' ),
        'button'  => array(),
    );
    $fw_options[] = array
    (
        'display'     => true,
        'advanced'    => false,
        'deps'        => '',
        'search'      => '',
        'type'        => 'textarea',
        'class'       => 'codemirror-php',
        'default'     => '', // Default value set by javascript snippet
        'placeholder' => '',
        'id'          => 'custom_php',
        'rows'        => 10,
        'title'       => '',
        'label'       => '',
        'desc'        => sprintf( __( "Enter here any PHP snippet you want to add to your site to modify default plugin's behavior. %sKeep the opening PHP tag%s %s&lt;?php%s on the first line.%sNo error checking is carried out upon saving, so make sure your code has no errors to avoid fatal errors on the frontend. However, your custom code is not run on the Dashboard, so you will always be able to come back here and revert any change.%s", 'molongui-authorship' ), '<b>', '</b>', '<code>', '</code>', '<br><div style="border-left:3px solid #fbb638; background:#f0f0f1; margin:1rem 3rem 0 0; font-family:Consolas,Monaco,monospace; font-size:11px; padding:8px">', '</div>' ),
        'help'        => array
        (
            'text' => sprintf( __( "%sYou can add any PHP snippet you wish, even if it is not related with this plugin.%s%sHowever, please note that the %ssnippets you add here will be only run on the frontend of your site%s. This way, we prevent you from losing access to your Dashboard in case of a fatal error. If you want to make it run on the Dashboard, open a support ticket with us, and we will explain how to do it.%s", 'molongui-authorship' ), '<p>', '</p>', '<p>', '<b>', '</b>', '</p>' ),
            'link' => array
            (
                'label'  => __( "Submit help request", 'molongui-authorship' ),
                'url'    => 'https://www.molongui.com/help/support/',
                'target' => '_blank',
            ),
        ),
    );
    $fw_options[] = array
    (
        'display'  => true,
        'advanced' => false,
        'type'     => 'header',
        'class'    => '',
        'label'    => __( "Uninstall", 'molongui-authorship' ),
        'buttons'  => array
        (
            'save' => array
            (
                'display'  => true,
                'type'     => 'save',
                'label'    => __( "Save", 'molongui-authorship' ),
                'title'    => __( "Save Settings", 'molongui-authorship' ),
                'class'    => 'm-save-options',
                'disabled' => true,
            ),
        ),
    );
    $fw_options[] = array
    (
        'display'  => true,
        'advanced' => false,
        'type'     => 'toggle',
        'class'    => '',
        'default'  => true,
        'id'       => 'keep_config',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sKeep this setting enabled to prevent config loss when removing the plugin from your site.%s %sKeeping plugin config might be useful on plugin reinstall or site migration.%s %sIf you want to completely remove all plugin config, uncheck this setting and then remove the plugin.%s", 'molongui-authorship' ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
        'label'    => __( "Keep plugin configuration for future use upon plugin uninstall.", 'molongui-authorship' ),
    );
    $fw_options[] = array
    (
        'display'  => true,
        'advanced' => false,
        'type'     => 'toggle',
        'class'    => '',
        'default'  => true,
        'id'       => 'keep_data',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sKeep this setting enabled to prevent data loss when removing the plugin from your site.%s %sKeeping plugin data might be useful on plugin reinstall or site migration.%s %sIf you want to completely remove any data added by the plugin since it was installed, uncheck this setting and then remove the plugin.%s", 'molongui-authorship' ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
        'label'    => __( "Keep plugin data for future use upon plugin uninstall.", 'molongui-authorship' ),
    );
}
$fw_options = apply_filters( 'authorship/options/common', $fw_options );