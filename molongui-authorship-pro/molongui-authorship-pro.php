<?php

/*!
 * Molongui Authorship Pro
 *
 * @package           Molongui Authorship Pro
 * @author            Molongui
 * @copyright         2015 Molongui
 *
 * @wordpress-plugin
 * Plugin Name:       Molongui Authorship Pro
 * Plugin URI:        https://www.molongui.com/wordpress-plugin-post-authors
 * Description:       Adds premium features to the Molongui Authorship plugin.
 * Version:           1.7.4
 * Requires at least: 5.2
 * Tested up to:      6.5
 * Requires PHP:      5.6.20
 * Author:            Molongui
 * Author URI:        https://www.molongui.com
 * Text Domain:       molongui-authorship-pro
 * Domain Path:       /i18n
 */

namespace Molongui\Authorship\Pro;

use Molongui\Authorship\Common\Utils\Debug;
use Molongui\Authorship\Common\Modules\DB_Update;
use Molongui\Authorship\Pro\Common\Modules\License;
use Molongui\Authorship\Pro\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
final class MolonguiAuthorshipPro
{
    const VERSION = '1.7.4';
    private static $_instance = null;
    const MINIMUM_CORE_VERSION = '4.9.2';
    const CORE_SLUG = 'molongui-authorship';
    private $core;
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( "Cloning instances of this class is forbidden.", 'molongui-authorship-pro' ), '1.0.0' );
    }
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( "Unserializing instances of this class is forbidden.", 'molongui-authorship-pro' ), '1.0.0' );
    }
    public static function instance()
    {
        if ( is_null( self::$_instance ) )
        {
            self::$_instance = new self();
            do_action( 'authorship_pro/loaded' );
        }

        return self::$_instance;
    }
    public function __construct()
    {
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
        if ( !$this->checks_passed() )
        {
            return false;
        }
        self::define_constants();
        require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'common/autoloader.php';
        register_activation_hook(   MOLONGUI_AUTHORSHIP_PRO_FILE, array( $this, 'activate'   ) );
        register_deactivation_hook( MOLONGUI_AUTHORSHIP_PRO_FILE, array( $this, 'deactivate' ) );
        add_action( 'wpmu_new_blog', array( $this, 'activate_on_new_blog' ), 10, 6 );
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 11 );
        return true;
    }
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain( 'molongui-authorship-pro', false, plugin_dir_path( __FILE__ ) . 'i18n/' );
    }
    public function checks_passed()
    {
        if ( version_compare( PHP_VERSION, '5.6.20', '<' ) )
        {
            add_action( 'admin_notices', array( $this, 'fail_php_error' ) );
            return false;
        }
        if ( version_compare( get_bloginfo( 'version' ), '5.2', '<' ) )
        {
            add_action( 'admin_notices', array( $this, 'fail_wp_error' ) );
            return false;
        }

        return true;
    }
    function fail_php_error()
    {
        $min_php_version = '5.6.20';

        if ( isset( $_GET['activate'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            unset( $_GET['activate'] );
        }

        /*! // translators: %1$s: <strong>. %2$s: </strong>. %3$s: Min required PHP version. %4$s: User PHP version */
        $message  = sprintf( esc_html__( '%1$sMolongui Authorship Pro%2$s requires PHP version %3$s or greater to operate. Unfortunately, your current PHP version (%4$s) is too old, so the plugin has been disabled.', 'molongui-authorship-pro' ), '<strong>', '</strong>', $min_php_version, PHP_VERSION );
        $message .= sprintf( '<p><a href="%s" class="button-primary" target="_blank">%s</a></p>', 'https://www.molongui.com/help/how-to-update-my-php-version/', __( "How to update PHP?", 'molongui-authorship-pro' ) );
        $html_message = sprintf( '<div class="notice notice-error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
    function fail_wp_error()
    {
        $min_wp_version = '5.2';

        if ( isset( $_GET['activate'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            unset( $_GET['activate'] );
        }

        /*! // translators: %1$s: <strong>.%2$s: </strong>. %3$s: Min required WordPress version */
        $message = sprintf( esc_html__( '%1$sMolongui Authorship Pro%2$s requires WordPress version %3$s or higher. Please update your WordPress to run this plugin.', 'molongui-authorship-pro' ), '<strong>', '</strong>', $min_wp_version );
        $html_message = sprintf( '<div class="notice notice-error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
    public function define_constants()
    {
        foreach( self::get_constants() as $name => $value )
        {
            if ( !defined( $name ) )
            {
                define( $name, $value );
            }
        }
    }
    public static function get_constants()
    {
        return array
        (
            'MOLONGUI_AUTHORSHIP_PRO_VERSION'    => self::VERSION,

            'MOLONGUI_AUTHORSHIP_PRO_FILE'       => __FILE__,                              // /var/www/domain/wp-content/plugins/molongui-authorship-pro/molongui-authorship-pro.php
            'MOLONGUI_AUTHORSHIP_PRO_DIR'        => plugin_dir_path( __FILE__ ),           // /var/www/domain/wp-content/plugins/molongui-authorship-pro/
            'MOLONGUI_AUTHORSHIP_PRO_FOLDER'     => basename( dirname( __FILE__ ) ),       // molongui-authorship-pro
            'MOLONGUI_AUTHORSHIP_PRO_URL'        => plugin_dir_url( __FILE__ ),            // https://domain.tld/wp-content/plugins/molongui-authorship-pro/

            'MOLONGUI_AUTHORSHIP_PRO_BASENAME'   => plugin_basename( __FILE__ ),           // molongui-authorship-pro/molongui-authorship-pro.php
            'MOLONGUI_AUTHORSHIP_PRO_NAMESPACE'  => '\Molongui\Authorship\Pro',
            'MOLONGUI_AUTHORSHIP_PRO_PREFIX'     => 'molongui_authorship_pro',
            'MOLONGUI_AUTHORSHIP_PRO_NAME'       => 'molongui-authorship-pro',
            'MOLONGUI_AUTHORSHIP_PRO_ID'         => 'authorship_pro',
            'MOLONGUI_AUTHORSHIP_PRO_TITLE'      => 'Authorship Pro',
            'MOLONGUI_AUTHORSHIP_PRO_DB_SCHEMA'  => 6,
            'MOLONGUI_AUTHORSHIP_PRO_DB_VERSION' => 'molongui_authorship_pro_db_version', // Options key
            'MOLONGUI_AUTHORSHIP_PRO_INSTALL'    => 'molongui_authorship_pro_install',    // Options key

            'MOLONGUI_AUTHORSHIP_PRO_CONTRIB_ID' => 'molongui_authorship_pro_contributors_page_id',
            'MOLONGUI_AUTHORSHIP_PRO_LANDING'    => 'https://www.molongui.com/wordpress-plugin-post-authors/',
        );
    }
    public function activate( $network_wide )
    {
        Activator::activate( $network_wide );
    }
    public function deactivate( $network_wide )
    {
        Deactivator::deactivate( $network_wide );
    }
    public function activate_on_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta )
    {
        Activator::activate_on_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta );
    }
    public function on_plugins_loaded()
    {
        if ( !did_action( 'authorship/loaded' ) )
        {
            add_action( 'admin_notices', array( $this, 'admin_notice_missing_core_plugin' ) );
            return false;
        }
        if ( self::is_disabled() )
        {
            if ( class_exists( '\Molongui\Authorship\Common\Utils\Debug' ) )
            {
                Debug::console_log( null, "The ".MOLONGUI_AUTHORSHIP_PRO_TITLE." plugin is disabled. Remove the 'noAuthorshipPro' query string from the URL in order to enable it." );
            }
            return false;
        }
        $license = new License();
        add_action( 'wp_ajax_'.MOLONGUI_AUTHORSHIP_PREFIX.'_activate_license_key'  , array( $license, 'activate_license_key'   ) );
        add_action( 'wp_ajax_'.MOLONGUI_AUTHORSHIP_PREFIX.'_deactivate_license_key', array( $license, 'deactivate_license_key' ) );

        if ( $this->is_compatible() )
        {
            $this->update_db();
            if ( !$license->is_active() )
            {
                add_filter( 'authorship/options/common', array( Settings::class, 'get_license_markup' ) );
                Debug::console_log( null, sprintf( "%s %s not running because there is no active license.", MOLONGUI_AUTHORSHIP_PRO_TITLE, MOLONGUI_AUTHORSHIP_PRO_VERSION ) );
                return false;
            }

            $this->init();
        }
    }
    private function is_disabled()
    {
        if ( !is_admin() and isset( $_GET['noAuthorshipPro'] ) and 0 !== $_GET['noAuthorshipPro'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            return true;
        }

        return false;
    }
    private function is_compatible()
    {
        if ( version_compare( MOLONGUI_AUTHORSHIP_VERSION, self::MINIMUM_CORE_VERSION, '<' ) )
        {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_core_version' ) );
            return false;
        }
        if ( defined( 'MOLONGUI_AUTHORSHIP_MIN_PRO' ) and version_compare( MOLONGUI_AUTHORSHIP_PRO_VERSION, MOLONGUI_AUTHORSHIP_MIN_PRO, '<' ) )
        {
            add_action( 'admin_notices', array( $this, 'admin_notice_outdated_pro_plugin' ) );
            return false;
        }

        return true;
    }
    private function update_db()
    {
        $update_db = new DB_Update( MOLONGUI_AUTHORSHIP_PRO_DB_SCHEMA, MOLONGUI_AUTHORSHIP_PRO_DB_VERSION, MOLONGUI_AUTHORSHIP_PRO_NAMESPACE );
        if ( $update_db->db_update_needed() )
        {
            $update_db->run_update();
        }
    }
    public function admin_notice_missing_core_plugin()
    {
        if ( isset( $_GET['activate'] ) )
        {
            unset( $_GET['activate'] );
        }

        /*! // translators: %1$s: <strong>. %2$s: </strong>. %3$s: <strong>. %4$s: </strong> */
        $message = sprintf( esc_html__( '%1$sMolongui Authorship Pro%2$s requires the core free plugin to be installed and activated. Because it is not, the plugin is currently %3$sNOT RUNNING%4$s.', 'molongui-authorship-pro' ), '<strong>', '</strong>', '<strong>', '</strong>' );

        if ( current_user_can( 'activate_plugins' ) )
        {
            if ( $this->is_core_installed() )
            {
                $activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin='.$this->core['basename'].'&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_'.$this->core['basename'] );
                $message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $activation_url, __( "Activate It Now", 'molongui-authorship-pro' ) );
            }
            else
            {
                $install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=molongui-authorship' ), 'install-plugin_molongui-authorship' );
                $message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $install_url, __( "Install It Now", 'molongui-authorship-pro' ) );
            }
        }

        $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
    private function is_core_installed()
    {
        $installed_plugins = get_plugins();
        $key = array_search( self::CORE_SLUG, array_map( function( $v ){ return $v['TextDomain']; }, $installed_plugins ) );

        if ( empty( $key ) )
        {
            return false;
        }
        $this->core = $installed_plugins[$key];
        $this->core['basename'] = $key;
        return version_compare( $installed_plugins[$key]['Version'], self::MINIMUM_CORE_VERSION, '>=' );
    }
    public function admin_notice_minimum_core_version()
    {
        if ( isset( $_GET['activate'] ) )
        {
            unset( $_GET['activate'] );
        }

        /*! // translators: %1$s: <strong>. %2$s: </strong>. %3$s: The core plugin version. %4$s: <strong>. %5$s: </strong> */
        $message = sprintf( esc_html__( '%1$sMolongui Authorship Pro%2$s requires Molongui authorship version %3$s or greater. Because you are using an earlier version, the plugin is currently %4$sNOT RUNNING%5$s.', 'molongui-authorship-pro' ), '<strong>', '</strong>', self::MINIMUM_CORE_VERSION, '<strong>', '</strong>' );

        if ( current_user_can( 'activate_plugins' ) )
        {
            $update_url = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . MOLONGUI_AUTHORSHIP_BASENAME, 'upgrade-plugin_'.MOLONGUI_AUTHORSHIP_BASENAME );
            $message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $update_url, __( "Update It Now", 'molongui-authorship-pro' ) );
        }

        $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
    public function admin_notice_outdated_pro_plugin()
    {
        if ( isset( $_GET['activate'] ) )
        {
            unset( $_GET['activate'] );
        }

        $message  = '<h2 style="margin: 1em 0">' . esc_html__( "Action Required: Incompatible Plugin Version Detected", 'molongui-authorship-pro' ) . '</h2>';
        /*! // translators: %s: Plugin name */
        $message .= sprintf( esc_html__( "Your current version of %s is outdated and not compatible with your site. As a result, the plugin has been temporarily disabled to ensure system stability. Please update to the latest version promptly to restore functionality.", 'molongui-authorship-pro' ), '<strong>Molongui Authorship Pro</strong>' );

        if ( current_user_can( 'activate_plugins' ) )
        {
            $check_url    = self_admin_url( 'update-core.php?force-check=1' );
            $update_url   = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . MOLONGUI_AUTHORSHIP_PRO_BASENAME, 'upgrade-plugin_'.MOLONGUI_AUTHORSHIP_PRO_BASENAME );
            $download_url = 'https://my.molongui.com/api-downloads';
            $help_url     = 'https://www.molongui.com/help/understanding-the-plugin-compatibility-message/';

            $message .= sprintf(
                '<p style="margin-top:1em"><a href="%s" class="button-primary">%s</a>&nbsp;&nbsp;<a href="%s" target="_blank" class="button">%s</a>&nbsp;&nbsp;<a href="%s" target="_blank" class="">%s</a>&nbsp;&nbsp;<a href="%s" target="_blank" class="">%s</a></p>',
                $update_url, __( "Update Now", 'molongui-authorship-pro' ),
                $check_url, __( "Update Check", 'molongui-authorship-pro' ),
                $download_url, __( "Download Latest", 'molongui-authorship-pro' ),
                $help_url, __( "Help me!", 'molongui-authorship-pro' )
            );
        }

        $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
    public function init()
    {
        $paths = array
        (
            MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/helpers/',
            MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/hooks/',
            MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/sitemaps/',
            MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/shortcodes/',
            MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/compat.php',
            MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/extend-wp-rest-api.php',

            MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/post.php',
            MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/settings.php',
        );
        foreach ( $paths as $path )
        {
            self::require_file( $path );
        }
        Debug::console_log( null, sprintf( "%s %s", MOLONGUI_AUTHORSHIP_PRO_TITLE, MOLONGUI_AUTHORSHIP_PRO_VERSION ) );
        do_action( 'authorship_pro/init' );
    }
    public static function require_file( $path )
    {
        if ( is_file( $path ) and file_exists( $path ) )
        {
            require_once $path;
        }
        elseif ( is_dir( $path ) )
        {
            foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path ) ) as $file )
            {

                if ( $file->isFile() and 'php' === $file->getExtension() and 'index.php' !== $file->getFilename() )
                {
                    require_once $file->getPathname();
                }
            }
        }
    }

} // class
MolonguiAuthorshipPro::instance();