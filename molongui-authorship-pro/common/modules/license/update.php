<?php

namespace Molongui\Authorship\Pro\Common\Modules\License;

use Molongui\Authorship\Common\Utils\Debug;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
trait Update
{
    public function check_for_update()
    {
        $this->plugin_name = untrailingslashit( plugin_basename( $this->file ) );
        if ( strpos( $this->plugin_name, '.php' ) !== 0 ) $this->slug = dirname( $this->plugin_name );
        else $this->slug = $this->plugin_name;
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );
        add_filter( 'plugins_api', array( $this, 'information_request' ), 10, 3 );
    }
    public function update_check( $transient )
    {
        if ( empty( $transient->checked ) ) return $transient;

        $args = array
        (
            'wc_am_action' => 'update',
            'slug'         => $this->slug,
            'plugin_name'  => $this->plugin_name,
            'version'      => $this->software_version,
            'product_id'   => $this->product_id,
            'api_key'      => !empty( $this->data[$this->wc_am_api_key_key] ) ? $this->data[$this->wc_am_api_key_key] : '',
            'instance'     => $this->wc_am_instance_id,
        );
        $response = json_decode( $this->send_query( $args ), true );
        if ( isset( $response['data']['error_code'] ) )
        {
            $this->error_message = $response['data']['error'];
            add_action( 'admin_notices', array($this, 'update_error') );
        }
        if ( isset( $response ) and is_array( $response ) and $response !== false and $response['success'] === true )
        {
            $new_ver  = (string) $response['data']['package']['new_version'];
            $curr_ver = (string) $this->software_version;

            $package = array
            (
                'id'             => $response['data']['package']['id'],
                'slug'           => $response['data']['package']['slug'],
                'plugin'         => $response['data']['package']['plugin'],
                'new_version'    => $response['data']['package']['new_version'],
                'url'            => $response['data']['package']['url'],
                'tested'         => $response['data']['package']['tested'],
                'package'        => $response['data']['package']['package'],
                'upgrade_notice' => $response['data']['package']['upgrade_notice'],
                'icons' => array
                (
                    'svg'     => '',
                    '1x'      => 'https://ps.w.org/'.MOLONGUI_AUTHORSHIP_NAME.'/assets/icon-128x128.png',
                    '2x'      => 'https://ps.w.org/'.MOLONGUI_AUTHORSHIP_NAME.'/assets/icon-256x256.png',
                    'default' => 'https://ps.w.org/'.MOLONGUI_AUTHORSHIP_NAME.'/assets/icon-128x128.png',
                ),
            );
            if ( isset( $new_ver ) and isset( $curr_ver ) )
            {
                if ( version_compare( $new_ver, $curr_ver, '>' ) )
                {
                    $update_available = true;
                    $transient->response[$this->plugin_name] = (object) $package;
                    unset( $transient->no_update[$this->plugin_name] );
                }
                else
                {
                    $item = array
                    (
                        'id'            => $this->plugin_name,
                        'slug'          => $this->slug,
                        'plugin'        => $this->plugin_name,
                        'new_version'   => $this->software_version,
                        'url'           => '',
                        'package'       => '',
                        'icons'         => array(),
                        'banners'       => array(),
                        'banners_rtl'   => array(),
                        'tested'        => '',
                        'requires_php'  => '',
                        'compatibility' => new \stdClass(),
                    );
                    $transient->no_update[$this->plugin_name] = (object) $item;
                }
            }
        }
        if ( $this->debug )
        {
            Debug::dump( array( 'plugin' => MOLONGUI_AUTHORSHIP_TITLE, 'function' => __FUNCTION__, 'arguments' => $args, 'plugin_information response' => $response, 'update?' => $update_available ? 'UPDATE AVAILABLE!' : 'NO update available :(', 'versions' => array( 'current' => $curr_ver, 'new' => $new_ver )/*, 'modified transient' => $transient['response']*/  ) );
        }

        return $transient;
    }
    public function information_request( $result, $action, $args )
    {
        if ( isset( $args->slug ) )
        {
            if ( $args->slug != $this->slug )
            {
                return $result;
            }
        }
        else
        {
            return $result;
        }

        $args = array
        (
            'wc_am_action' => 'plugininformation',
            'plugin_name'  => $this->plugin_name,
            'version'      => $this->software_version,
            'product_id'   => $this->product_id,
            'api_key'      => !empty( $this->data[$this->wc_am_api_key_key] ) ? $this->data[$this->wc_am_api_key_key] : '',
            'instance'     => $this->wc_am_instance_id,
            'object'       => $this->wc_am_domain,
        );

        $response = unserialize( $this->send_query( $args ) );

        if ( isset( $response ) and is_object( $response ) and $response !== false )
        {
            $response->banners = array
            (
                'low'  => 'https://ps.w.org/'.MOLONGUI_AUTHORSHIP_NAME.'/assets/banner-772x250.png',
                'high' => 'https://ps.w.org/'.MOLONGUI_AUTHORSHIP_NAME.'/assets/banner-1544x500.png',
            );
            if ( empty( $response->sections['description'] ) )  unset( $response->sections['description'] );
            if ( empty( $response->sections['installation'] ) ) unset( $response->sections['installation'] );
            if ( empty( $response->sections['faq'] ) )          unset( $response->sections['faq'] );
            if ( empty( $response->sections['screenshots'] ) )  unset( $response->sections['screenshots'] );
            if ( empty( $response->sections['other_notes'] ) )  unset( $response->sections['other_notes'] );
            unset( $response->active_installs );
            return $response;
        }

        return $result;
    }
    public function try_automatic_updates()
    {
        global $wp_version;

        if ( version_compare( $wp_version, '5.5', '>=' ) )
        {
                add_filter( 'auto_update_plugin', array( $this, 'maybe_auto_update' ), 10, 2 );
        }
    }
    public function maybe_auto_update( $update, $item )
    {
        if ( isset( $item->slug ) and $item->slug == $this->slug )
        {
            if ( $this->is_auto_update_disabled() )
            {
                return false;
            }
            if ( !$this->is_active() or !$this->is_active( true ) )
            {
                return false;
            }

            return true;
        }

        return $update;
    }
    public function is_auto_update_disabled()
    {
        if ( defined( 'DISALLOW_FILE_MODS' ) and DISALLOW_FILE_MODS )
        {
            return true;
        }

        if ( defined( 'WP_INSTALLING' ) )
        {
            return true;
        }

        $wp_updates_disabled = defined( 'AUTOMATIC_UPDATER_DISABLED' ) and AUTOMATIC_UPDATER_DISABLED;
        $wp_updates_disabled = apply_filters( 'authorship_pro/automatic_updater_disabled', $wp_updates_disabled );

        if ( $wp_updates_disabled )
        {
            return true;
        }

        return false;
    }
    public function auto_update_message( $html, $plugin_file, $plugin_data )
    {
        if ( $this->wc_am_plugin_name == $plugin_file )
        {
            global $status, $page;
            if ( !$this->is_active() or !$this->is_active( true ) )
            {
                return esc_html__( 'Auto-updates unavailable', 'molongui-authorship-pro' );
            }

            $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
            $html         = array();

            if ( !empty( $plugin_data[ 'auto-update-forced' ] ) )
            {
                if ( $plugin_data[ 'auto-update-forced' ] )
                {
                    $text = __( 'Auto-updates enabled' );
                }
                else
                {
                    $text = __( 'Auto-updates disabled' );
                }

                $action     = 'unavailable';
                $time_class = ' hidden';
            }
            elseif ( in_array( $plugin_file, $auto_updates, true ) )
            {
                $text       = __( 'Disable auto-updates' );
                $action     = 'disable';
                $time_class = '';
            }
            else
            {
                $text       = __( 'Enable auto-updates' );
                $action     = 'enable';
                $time_class = ' hidden';
            }

            $query_args = array(
                'action'        => "{$action}-auto-update",
                'plugin'        => $plugin_file,
                'paged'         => $page,
                'plugin_status' => $status,
            );

            $url = add_query_arg( $query_args, 'plugins.php' );

            if ( 'unavailable' === $action )
            {
                $html[] = '<span class="label">' . $text . '</span>';
            }
            else
            {
                $html[] = sprintf( '<a href="%s" class="toggle-auto-update aria-button-if-js" data-wp-action="%s">', wp_nonce_url( $url, 'updates' ), $action );

                $html[] = '<span class="dashicons dashicons-update spin hidden" aria-hidden="true"></span>';
                $html[] = '<span class="label">' . $text . '</span>';
                $html[] = '</a>';
            }

            if ( !empty( $plugin_data[ 'update' ] ) )
            {
                $html[] = sprintf( '<div class="auto-update-time%s">%s</div>', $time_class, wp_get_auto_update_message() );
            }

            $html = implode( '', $html );
        }

        return $html;
    }
    public function update_error()
    {
        if ( !isset( $this->error_message ) or empty( $this->error_message ) )
        {
            $plugins     = get_plugins();
            $plugin_name = isset( $plugins[$this->plugin_name] ) ? $plugins[$this->plugin_name]['Name'] : $this->plugin_name;

            $this->error_message = sprintf( esc_html__( "%sError%s: Checking for %s updates failed. It might just be a temporary issue. If the issue persists for days, please open a support ticket %shere%s.", 'molongui-authorship-pro' ), '<strong>', '</strong>', $plugin_name, '<a href="https://www.molongui.com/help/support/" target="_blank">', '</a>' );
        }
        self::display_error_notice( 'update-notice-dismissal', $this->error_message, array(), '30' );
    }

} // trait