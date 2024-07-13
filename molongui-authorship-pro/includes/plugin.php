<?php

namespace Molongui\Authorship\Pro;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
final class Plugin
{
    const MINIMUM_CORE_VERSION = '4.8.0';
    const CORE_SLUG = 'molongui-authorship';
    private static $_instance = null;
    private $core;
    public function __clone()
    {
        \_doing_it_wrong( __FUNCTION__, \esc_html__( "Cloning instances of this class is forbidden.", 'molongui-authorship-pro' ), '1.3.0' );
    }
    public function __wakeup()
    {
        \_doing_it_wrong( __FUNCTION__, \esc_html__( "Unserializing instances of this class is forbidden.", 'molongui-authorship-pro' ), '1.3.0' );
    }
    public static function instance()
    {
        if ( \is_null( self::$_instance ) )
        {
            self::$_instance = new self();
            \do_action( 'authorship_pro/loaded' );
        }

        return self::$_instance;
    }
    public function __construct()
    {
        require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'config/plugin.php';
        require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/autoloader.php';
        \register_activation_hook(   MOLONGUI_AUTHORSHIP_PRO_FILE, array( $this, 'activate'   ) );
        \register_deactivation_hook( MOLONGUI_AUTHORSHIP_PRO_FILE, array( $this, 'deactivate' ) );
        \add_action( 'wpmu_new_blog', array( $this, 'activate_on_new_blog' ), 10, 6 );
        \add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 11 );
        return true;
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
        if ( !\did_action( 'authorship/loaded' ) )
        {
            \add_action( 'admin_notices', array( $this, 'admin_notice_missing_core_plugin' ) );
            return false;
        }
        $license = new \Molongui\Authorship\Pro\Update\License();
        \add_action( 'wp_ajax_'.MOLONGUI_AUTHORSHIP_PREFIX.'_activate_license_key'  , array( $license, 'activate_license_key'   ) );
        \add_action( 'wp_ajax_'.MOLONGUI_AUTHORSHIP_PREFIX.'_deactivate_license_key', array( $license, 'deactivate_license_key' ) );

        if ( $this->is_compatible() )
        {
            $this->update_db();
            if ( !$license->is_active() )
            {
                require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/hooks/common/options/license.php';
                require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/hooks/options/license.php';
                return;
            }

            $this->init();
        }
    }
    private function is_compatible()
    {
        if ( \version_compare( MOLONGUI_AUTHORSHIP_VERSION, self::MINIMUM_CORE_VERSION, '<' ) )
        {
            \add_action( 'admin_notices', array( $this, 'admin_notice_minimum_core_version' ) );
            return false;
        }
        if ( \defined( 'MOLONGUI_AUTHORSHIP_MIN_PRO' ) and \version_compare( MOLONGUI_AUTHORSHIP_PRO_VERSION, MOLONGUI_AUTHORSHIP_MIN_PRO, '<' ) )
        {
            \add_action( 'admin_notices', array( $this, 'admin_notice_outdated_pro_plugin' ) );
            return false;
        }

        return true;
    }
    private function update_db()
    {
        $update_db = new \Molongui\Authorship\Common\Modules\DB_Update( MOLONGUI_AUTHORSHIP_PRO_DB, MOLONGUI_AUTHORSHIP_PRO_DB_VERSION, MOLONGUI_AUTHORSHIP_PRO_NAMESPACE );
        if ( $update_db->db_update_needed() ) $update_db->run_update();
    }
    public function admin_notice_missing_core_plugin()
    {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        /*! translators: 1: <strong> 2: </strong> 3: <strong> 4: </strong> */
        $message = \sprintf( \esc_html__( '%1$sMolongui Authorship Pro%2$s requires the core free plugin to be installed and activated. Because it is not, the plugin is currently %3$sNOT RUNNING%4$s.', 'molongui-authorship-pro' ), '<strong>', '</strong>', '<strong>', '</strong>' );

        if ( \current_user_can( 'activate_plugins' ) )
        {
            if ( $this->is_core_installed() )
            {
                $activation_url = \wp_nonce_url( 'plugins.php?action=activate&amp;plugin='.$this->core['basename'].'&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_'.$this->core['basename'] );
                $message .= \sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $activation_url, __( "Activate It Now", 'molongui-authorship-pro' ) );
            }
            else
            {
                $install_url = \wp_nonce_url( \self_admin_url( 'update.php?action=install-plugin&plugin=molongui-authorship' ), 'install-plugin_molongui-authorship' );
                $message .= \sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $install_url, __( "Install It Now", 'molongui-authorship-pro' ) );
            }
        }

        $html_message = \sprintf( '<div class="error">%s</div>', \wpautop( $message ) );
        echo \wp_kses_post( $html_message );
    }
    private function is_core_installed()
    {
        $installed_plugins = \get_plugins();
        $key = \array_search( self::CORE_SLUG, \array_map( function( $v ){ return $v['TextDomain']; }, $installed_plugins ) );

        if ( empty( $key ) )
        {
            return false;
        }
        $this->core = $installed_plugins[$key];
        $this->core['basename'] = $key;
        return \version_compare( $installed_plugins[$key]['Version'], self::MINIMUM_CORE_VERSION, '>=' );
    }
    public function admin_notice_minimum_core_version()
    {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        /*! translators: 1: <strong> 2: </strong> 3: Core plugin version 4: <strong> 5: </strong> */
        $message = \sprintf( \esc_html__( '%1$sMolongui Authorship Pro%2$s requires Molongui Authorship version %3$s or greater. Because you are using an earlier version, the plugin is currently %4$sNOT RUNNING%5$s.', 'molongui-authorship-pro' ), '<strong>', '</strong>', self::MINIMUM_CORE_VERSION, '<strong>', '</strong>' );

        if ( \current_user_can( 'activate_plugins' ) )
        {
            $update_url = \wp_nonce_url( \self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . MOLONGUI_AUTHORSHIP_BASENAME, 'upgrade-plugin_'.MOLONGUI_AUTHORSHIP_BASENAME );
            $message .= \sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $update_url, __( "Update It Now", 'molongui-authorship-pro' ) );
        }

        $html_message = \sprintf( '<div class="error">%s</div>', \wpautop( $message ) );
        echo \wp_kses_post( $html_message );
    }
    public function admin_notice_outdated_pro_plugin()
    {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $url = \self_admin_url( 'update-core.php?force-check=1' );

        /*! translators: 1: <strong> 2: </strong> 3: <strong> 4: </strong> */
        $message  = \sprintf( \esc_html__( 'Installed version of %1$sMolongui Authorship Pro%2$s is too old to run on your site. So the plugin is currently %3$sNOT RUNNING%4$s. Please update it.', 'molongui-authorship-pro' ), '<strong>', '</strong>', '<strong>', '</strong>' );
        $message .= \sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $url, __( "Update Plugin", 'molongui-authorship-pro' ) );

        $html_message = \sprintf( '<div class="error">%s</div>', \wpautop( $message ) );
        echo \wp_kses_post( $html_message );
    }
    public function init()
    {
        require_once MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/load.php';
        authorship_debug( null, sprintf( "%s %s", MOLONGUI_AUTHORSHIP_PRO_TITLE, MOLONGUI_AUTHORSHIP_PRO_VERSION ) );
        do_action( 'authorship_pro/init' );
    }

} // class
Plugin::instance();