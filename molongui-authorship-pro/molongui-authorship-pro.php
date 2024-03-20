<?php
defined( 'ABSPATH' ) or exit;

/*!
 * Plugin Name:       Molongui Authorship Pro
 * Description:       Adds premium features to the Molongui Authorship plugin.
 * Plugin URI:        https://www.molongui.com/authorship/
 * Text Domain:       molongui-authorship-pro
 * Domain Path:       /i18n/
 * Requires PHP:      5.5.0
 * Requires at least: 5.2
 * Tested up to:      6.4
 * Author:            Molongui
 * Author URI:        https://www.molongui.com/
 * Version:           1.6.7
 */
add_action( 'plugins_loaded', 'molongui_authorship_pro_load_plugin_textdomain' );
if ( version_compare( PHP_VERSION, '5.5', '<' ) )
{
    add_action( 'admin_notices', 'molongui_authorship_pro_fail_php_version' );
}
elseif ( version_compare( get_bloginfo( 'version' ), '5.2', '<' ) )
{
    add_action( 'admin_notices', 'molongui_authorship_pro_fail_wp_version' );
}
else
{
    define( 'MOLONGUI_AUTHORSHIP_PRO_VERSION', '1.6.7' );
    define( 'MOLONGUI_AUTHORSHIP_PRO_FILE', __FILE__ );
    define( 'MOLONGUI_AUTHORSHIP_PRO_DIR', plugin_dir_path( MOLONGUI_AUTHORSHIP_PRO_FILE ) );
    require MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/plugin.php';
}
function molongui_authorship_pro_load_plugin_textdomain()
{
    load_plugin_textdomain( 'molongui-authorship-pro', false, dirname( __FILE__ ) . '/i18n/' );
}
function molongui_authorship_pro_fail_php_version()
{
    if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

    /*! translators: 1: <strong> 2: </strong> 3: PHP version 4: <strong> 5: </strong> */
    $message  = sprintf( esc_html__( '%1$sMolongui Authorship Pro%2$s requires PHP version %3$s or greater. Because you are using an earlier version, the plugin is currently %4$sNOT RUNNING%5$s.', 'molongui-authorship-pro' ), '<strong>', '</strong>', '5.5', '<strong>', '</strong>' );
    $message .= sprintf( '<p><a href="%s" class="button-primary" target="_blank">%s</a></p>', 'https://www.molongui.com/help/how-to-update-my-php-version/', __( "How to update PHP?", 'molongui-authorship-pro' ) );
    $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
    echo wp_kses_post( $html_message );
}
function molongui_authorship_pro_fail_wp_version()
{
    if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

    /*! translators: 1: <strong> 2: </strong> 3: WordPress version 4: <strong> 5: </strong> */
    $message = sprintf( esc_html__( '%1$sMolongui Authorship Pro%2$s requires WordPress version %3$s+. Because you are using an earlier version, the plugin is currently %4$sNOT RUNNING%5$s.', 'molongui-authorship-pro' ), '5.2' );
    $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
    echo wp_kses_post( $html_message );
}