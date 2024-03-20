<?php

namespace Molongui\Authorship\Pro\Includes\Update;
\defined( 'ABSPATH' ) or exit;
if ( !\class_exists( 'Molongui\Authorship\Pro\Includes\Update\License' ) )
{
    class License
    {
        use Key, Update;
        public $file             = MOLONGUI_AUTHORSHIP_PRO_FILE;
        public $software_title   = MOLONGUI_AUTHORSHIP_PRO_TITLE;
        public $software_version = MOLONGUI_AUTHORSHIP_PRO_VERSION;
        public $api_url          = 'https://www.molongui.com/';
        public $product_id       = '';
        public $plugin_name             = '';
        public $slug                    = '';
        public $data_key                = '';
        public $data                    = array();
        public $identifier              = '';
        public $no_product_id           = false;
        public $product_id_chosen       = 0;
        public $wc_am_activated_key     = '';
        public $wc_am_api_key_key       = '';
        public $wc_am_domain            = '';
        public $wc_am_instance_id       = '';
        public $wc_am_instance_key      = '';
        public $wc_am_plugin_name       = '';
        public $wc_am_product_id        = '';
        public $wc_am_renew_license_url = '';
        public $wc_am_software_version  = '';
        public $db_key_prefix       = MOLONGUI_AUTHORSHIP_PRO_PREFIX;
        public $data_instance_key   = 'instance';
        public $data_product_id_key = 'product_id';
        public $data_version_key    = 'version';
        public $data_activated_key  = 'status';
        public $data_expiry_key     = 'expiry';
        public $data_keep_key       = 'keep';
        public $error_message       = '';
        public $debug               = false;
        public function __construct()
        {
            $product_id = '';

            $this->no_product_id = empty( $product_id ) ? true : false;

            if ( $this->no_product_id )
            {
                $this->identifier        = \dirname( \untrailingslashit( \plugin_basename( $this->file ) ) );
                $product_id              = \strtolower( \str_ireplace( array( ' ', '_', '&', '?', '-' ), '_', $this->identifier ) );
                $this->wc_am_product_id  = $product_id . '_product_id';
                $this->product_id_chosen = \get_option( $this->wc_am_product_id );
            }
            else
            {
                if ( \is_int( $product_id ) ) $this->product_id = \absint( $product_id );
                else  $this->product_id = \esc_attr( $product_id );
            }
            if ( empty( $this->product_id ) and !empty( $this->product_id_chosen ) )
            {
                $this->product_id = $this->product_id_chosen;
            }
            $this->data_key            = $this->db_key_prefix . '_license';
            $this->wc_am_activated_key = $this->db_key_prefix . '_activated';
            $this->wc_am_instance_key  = $this->db_key_prefix . '_instance';
            $this->wc_am_api_key_key   = 'key'; //$this->data_key . '_api_key';
            $this->data                    = \get_option( $this->data_key );
            $this->wc_am_instance_id       = \get_option( $this->wc_am_instance_key );
            $this->wc_am_domain = \str_ireplace( array( 'http://', 'https://' ), '', \home_url() );
            $this->check_for_update();
            \add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );
            \add_action( 'admin_notices', array( $this, 'inactive_notice' ), 999 );
            \add_action( 'admin_notices', array( $this, 'renew_notice' ), 999 );
        }
public function migrate_old_data( $product_id, $software_title )
{
    $upgraded_postfix = \strtolower( \str_ireplace( array( ' ', '_', '&', '?', '-' ), '_', $product_id ) );
    $upgraded         = \get_option( 'wc_client_20_ugrade_attempt_' . $upgraded_postfix );

    if ( $upgraded != 'yes' )
    {
        $title        = \is_int( $product_id ) ? \strtolower( $software_title ) : \strtolower( $product_id );
        $title        = \str_ireplace( array( ' ', '_', '&', '?' ), '_', $title );
        $old_data_key = $title . '_license';
        $data         = \get_option( $old_data_key );
        $instance     = \get_option( $title . '_instance' );

        if ( !empty( $data ) and !empty( $instance ) )
        {
            $api_key = array
            (
                $this->wc_am_api_key_key => $data['activation_key'],
                $this->data_expiry_key   => $data['expiration_date'],
                $this->data_keep_key     => $data['keep_license'],
            );

            \update_option( $this->data_key, $api_key );
            \update_option( $this->wc_am_instance_key, $instance );
            !empty( $instance ) ? \update_option( $this->wc_am_activated_key, 'Activated' ) : \update_option( $this->wc_am_activated_key, 'Deactivated' );
            \update_option( 'wc_client_20_ugrade_attempt_' . $upgraded_postfix, 'yes' );
        }
        else
        {
            if ( empty( $this->wc_am_instance_id ) )
            {
                \add_action( 'admin_notices', array( $this, 'migrate_error_notice' ) );
            }
        }
    }
}
public function migrate_error_notice()
        {
            ?>
            <div class="notice notice-error">
                <p>
                    <?php \esc_html_e( "Attempt to migrate data failed. Deactivate then reactive this plugin, then enter your License Key on the settings screen to receive software updates. Contact support if assistance is required.", 'molongui-authorship-pro' ); ?>
                </p>
            </div>
            <?php
        }
        public function init()
        {
            $instance_exists = \get_option( $this->wc_am_instance_key );

            if ( $this->data === false or $instance_exists === false )
            {
                if ( $instance_exists === false )
                {
                    \update_option( $this->wc_am_instance_key, \wp_generate_password( 12, false ) );
                }

                if ( $this->data === false )
                {
                    $defaults = array
                    (
                        $this->data_product_id_key => '',
                        $this->data_version_key    => '',
                        $this->wc_am_api_key_key   => '',
                        $this->data_expiry_key     => '',
                        $this->data_keep_key       => '1',
                    );
                    \update_option( $this->data_key, $defaults );
                }

                \update_option( $this->wc_am_activated_key, 'Deactivated' );
            }
        }
        public function remove( $force = false )
        {
            if ( $force == false and $this->data[$this->data_keep_key] ) return;
            $this->deactivate_key();
            $license_options = array
            (
                $this->data_key,
                $this->wc_am_instance_key,
                $this->wc_am_activated_key,
            );
            if ( \is_multisite() )
            {
                global $blog_id;

                \switch_to_blog( $blog_id );

                foreach ( $license_options as $option ) \delete_option( $option );

                \restore_current_blog();
            }
            else
            {
                foreach ( $license_options as $option ) \delete_option( $option );
            }
        }
        public function deactivate_key()
        {
            $args = array
            (
                'api_key' => $this->data[$this->wc_am_api_key_key],
            );

            if ( $this->is_active() and $this->data[$this->wc_am_api_key_key] != '' )
            {
                $this->deactivate( $args );
            }
        }
        public function check_external_blocking()
        {
            if ( \defined( 'WP_HTTP_BLOCK_EXTERNAL' ) and WP_HTTP_BLOCK_EXTERNAL === true )
            {
                $host = \parse_url( $this->api_url, PHP_URL_HOST );

                if ( !\defined( 'WP_ACCESSIBLE_HOSTS' ) or \stristr( WP_ACCESSIBLE_HOSTS, $host ) === false )
                {
                    ?>
                    <div class="notice notice-error">
                        <p><?php \printf( __( "<b>Warning!</b> You're blocking external requests which means you won't be able to get %s updates. Please add %s to %s.", 'molongui-authorship-pro' ), $this->software_title, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
                    </div>
                    <?php
                }
            }
        }
        public function inactive_notice()
        {
            if ( !\apply_filters( 'boilerplate_hook_pro/show/inactive/notice', true ) ) return;
            if ( !$this->is_active() )
            {
	            $n_slug = 'inactive-license';
	            $notice = array
	            (
		            'id'          => $n_slug.'-notice-dismissal',
		            'type'        => 'error',
		            'content'     => array
		            (
			            'image'   => '',
			            'title'   => __( 'License not activated', 'molongui-authorship-pro' ),
			            'message' => \sprintf( __( "No license for %s has been found, so the plugin won't work! %sClick here%s to activate your copy of the plugin.", 'molongui-authorship-pro' ),
							            $this->software_title,
							            '<a href="' . \esc_url( \admin_url( 'admin.php?page=' . MOLONGUI_AUTHORSHIP_NAME . '&tab=' . MOLONGUI_AUTHORSHIP_PRO_PREFIX . '_' . 'license' ) ) . '">',
							            '</a>' ),
			            'buttons' => array(),
			            'button'  => array
			            (
				            'id'     => '',
				            'href'   => \esc_url( \admin_url( 'admin.php?page=' . MOLONGUI_AUTHORSHIP_NAME . '&tab=' . MOLONGUI_AUTHORSHIP_PRO_PREFIX . '_' . 'license' ) ),
				            'target' => '_self',
				            'class'  => '',
				            'icon'   => '',
				            'label'  => __( 'Activate plugin', 'molongui-authorship-pro' ),
			            ),
		            ),
		            'dismissible' => false,
		            'dismissal'   => 0,
		            'class'       => 'molongui-notice molongui-notice-red molongui-notice-icon-alert',
		            'pages'       => array
		            (
			            'dashboard' => 'dashboard',
			            'updates'   => 'update-core',
			            'plugins'   => 'plugins',
                        'plugin'    => 'molongui_page_'.MOLONGUI_AUTHORSHIP_NAME,
		            ),
	            );
                if ( defined( 'MOLONGUI_AUTHORSHIP_CPT' ) )
                {
                    $notice['pages']['cpt']      = MOLONGUI_AUTHORSHIP_CPT;
                    $notice['pages']['edit-cpt'] = 'edit-'.MOLONGUI_AUTHORSHIP_CPT;
                }
                \authorship_notice_display( $notice['id'], $notice['type'], $notice['content'], $notice['dismissible'], $notice['dismissal'], $notice['class'], $notice['pages'] );
            }
        }
        public function renew_notice()
        {
            if ( \apply_filters( 'boilerplate_hook_pro/hide_renew_notice', false ) ) return;
            if ( !$this->is_active() ) return;
            $expired = $this->is_expired();
            if ( $expired === 'about' )
            {
	            $n_slug = 'renew-license';
	            $notice = array
	            (
		            'id'          => $n_slug.'-notice-dismissal',
		            'type'        => 'warning',
		            'content'     => array
		            (
			            'image'   => '',
			            'icon'    => '',
			            'title'   => __( "Your license expires soon.", 'molongui-authorship-pro' ),
			            'message' => \sprintf( __( "%sRenew your license today%s to keep getting feature updates and premium support for %s plugin.", 'molongui-authorship-pro' ),
				                              '<a href="'.$this->api_url.'" target="_blank" >', '</a>', $this->software_title ),
			            'buttons' => array(),
			            'button'  => array
			            (
				            'id'     => '',
				            'href'   => $this->api_url,
				            'target' => '_blank',
				            'class'  => '',
				            'icon'   => '',
				            'label'  => __( "Renew license", 'molongui-authorship-pro' ),
			            ),
		            ),
		            'dismissible' => true,
		            'dismissal'   => 7,
		            'class'       => 'molongui-notice molongui-notice-orange molongui-notice-icon-info',
		            'pages'       => array
		            (
			            'dashboard' => 'dashboard',
			            'updates'   => 'update-core',
			            'plugins'   => 'plugins',
                        'plugin'    => 'molongui_page_'.MOLONGUI_AUTHORSHIP_NAME,
		            ),
	            );
                if ( defined( 'MOLONGUI_AUTHORSHIP_CPT' ) )
                {
                    $notice['pages']['cpt']      = MOLONGUI_AUTHORSHIP_CPT;
                    $notice['pages']['edit-cpt'] = 'edit-'.MOLONGUI_AUTHORSHIP_CPT;
                }
                \authorship_notice_display( $notice['id'], $notice['type'], $notice['content'], $notice['dismissible'], $notice['dismissal'], $notice['class'], $notice['pages'] );
            }
            elseif ( $expired )
            {
	            $n_slug = 'expired-license';
	            $notice = array
	            (
		            'id'          => $n_slug.'-notice-dismissal',
		            'type'        => 'error',
		            'content'     => array
		            (
			            'image'   => '',
			            'icon'    => '',
			            'title'   => __( "Your license has expired!", 'molongui-authorship-pro' ),
			            'message' => \sprintf( __( "%sRenew your license today%s to get feature updates and premium support for %s plugin.", 'molongui-authorship-pro' ),
				                              '<a href="'.$this->api_url.'" target="_blank" >', '</a>', $this->software_title ),
			            'buttons' => array(),
			            'button'  => array
			            (
				            'id'     => '',
				            'href'   => $this->api_url,
				            'target' => '_blank',
				            'class'  => '',
				            'icon'   => '',
				            'label'  => __( "Renew license", 'molongui-authorship-pro' ),
			            ),
		            ),
		            'dismissible' => true,
		            'dismissal'   => 60,
		            'class'       => 'molongui-notice molongui-notice-red molongui-notice-icon-alert',
		            'pages'       => array
		            (
			            'dashboard' => 'dashboard',
			            'updates'   => 'update-core',
			            'plugins'   => 'plugins',
                        'plugin'    => 'molongui_page_'.MOLONGUI_AUTHORSHIP_NAME,
		            ),
	            );
                if ( defined( 'MOLONGUI_AUTHORSHIP_CPT' ) )
                {
                    $notice['pages']['cpt']      = MOLONGUI_AUTHORSHIP_CPT;
                    $notice['pages']['edit-cpt'] = 'edit-'.MOLONGUI_AUTHORSHIP_CPT;
                }
                \authorship_notice_display( $notice['id'], $notice['type'], $notice['content'], $notice['dismissible'], $notice['dismissal'], $notice['class'], $notice['pages'] );
            }
        }
        public function is_active( $live = false )
        {
            if ( $live )
            {
                $args = array
                (
                    'api_key' => $this->data[$this->wc_am_api_key_key],
                );

                $response = \json_decode( $this->status( $args ), true );

                return !empty( $response['data']['activated'] ) and $response['data']['activated'];
            }
            return \get_option( $this->wc_am_activated_key ) == 'Activated';
        }
        public function is_expired()
        {
            if ( empty( $this->data[$this->data_expiry_key] ) ) return false;
            $date = \DateTime::createFromFormat( 'Y-m-d', $this->data[$this->data_expiry_key], new \DateTimeZone( 'GMT' ) );
            $expiry = $date->getTimestamp();
            $days = 30;
            $date->modify("+$days days");
            $alert = $date->getTimestamp();
            $now = \time();
            if ( $now >= $alert and $now <= $expiry ) return 'about';
            elseif ( $now > $expiry ) return true;
            return false;
        }
        public function validate_options( $input )
        {
            $options                           = $this->data;
            $options[$this->wc_am_api_key_key] = \trim( $input[$this->wc_am_api_key_key] );
            $api_key                           = \trim( $input[$this->wc_am_api_key_key] );
            $activation_status                 = \get_option( $this->wc_am_activated_key );
            $current_api_key                   = $this->data[$this->wc_am_api_key_key];

            if ( $this->no_product_id )
            {
                $new_product_id = \absint( $_REQUEST[$this->wc_am_product_id] );

                if ( !empty( $new_product_id ) )
                {
                    \update_option( $this->wc_am_product_id, $new_product_id );
                    $this->product_id = $new_product_id;
                }
            }
            if ( !$this->is_active() or $activation_status == '' or $api_key == '' or $api_key != $current_api_key )
            {
                if ( !empty( $current_api_key ) and ( $current_api_key != $api_key ) ) $this->replace_license_key( $current_api_key );

                $args = array
                (
                    'api_key' => $api_key,
                );
                $activate_results = \json_decode( $this->activate( $args ), true );
                if ( $activate_results['success'] === true and $activate_results['activated'] === true )
                {
                    \add_settings_error( 'activate_text', 'activate_msg', __( "Plugin activated.", 'molongui-authorship-pro' ) . ' ' . \esc_attr( "{$activate_results['message']}." ), 'updated' );
                    \update_option( $this->wc_am_activated_key, 'Activated' );

                    $options[$this->data_instance_key]   = $this->wc_am_instance_id;
                    $options[$this->data_product_id_key] = $this->product_id;
                    $options[$this->data_version_key]    = $this->software_version;
                    $options[$this->data_activated_key]  = 'Activated';
                    $options[$this->wc_am_api_key_key]   = $api_key;
                    $options[$this->data_expiry_key]     = empty( $activate_results['data']['expiry_date'] ) ? '' : \date( 'Y-m-d', $activate_results['data']['expiry_date'] );
                }
                if ( $activate_results == false and !empty( $this->data ) and !empty( $this->wc_am_activated_key ) )
                {
                    \add_settings_error( 'api_key_check_text', 'api_key_check_error', \esc_html__( "Connection failed to the Molongui Licensing Server. Try again later. There may be a problem on your server preventing outgoing requests, or the store is blocking your request to activate the plugin.", 'molongui-authorship-pro' ), 'error' );
                    \update_option( $this->wc_am_activated_key, 'Deactivated' );
                    \update_option( $this->wc_am_product_id, '' );
                    $options[$this->data_product_id_key] = '';
                    $options[$this->data_version_key]    = '';
                    $options[$this->data_activated_key]  = 'Deactivated';
                    $options[$this->wc_am_api_key_key]   = '';
                    $options[$this->data_expiry_key]     = '';
                }
                if ( isset( $activate_results['data']['error_code'] ) and !empty( $this->data ) and !empty( $this->wc_am_activated_key ) )
                {
                    \add_settings_error( 'wc_am_client_error_text', 'wc_am_client_error', \esc_attr( "{$activate_results['data']['error']}" ), 'error' );
                    \update_option( $this->wc_am_activated_key, 'Deactivated' );
                    \update_option( $this->wc_am_product_id, '' );
                    $options[$this->data_product_id_key] = '';
                    $options[$this->data_version_key]    = '';
                    $options[$this->data_activated_key]  = 'Deactivated';
                    $options[$this->wc_am_api_key_key]   = '';
                    $options[$this->data_expiry_key]     = '';
                }
            }

            return $options;
        }
        public function activate_license_key()
        {
            if ( !\check_ajax_referer( 'mfw_license_nonce', 'nonce', false ) )
            {
                $result = "error";
                $notice = __( "Security token check failed. Please make sure you're activating the license from the plugin settings page. If you already are, please refresh the page.", 'molongui-authorship-pro' );
                echo \json_encode( array( $result, $notice ) );
                \wp_die();
            }

            $result = "error";
            $notice = __( "It is like the provided license key is already active on this site. Please log in to your My Account and check your key state.", 'molongui-authorship-pro' );
            $options           = $this->data;
            $keep_license      = $options[$this->data_keep_key];
            $api_key           = \trim( $_POST['key'] );
            $activation_status = \get_option( $this->wc_am_activated_key );
            $current_api_key   = $this->data[$this->wc_am_api_key_key];

            if ( $this->no_product_id )
            {
                $new_product_id = \absint( \trim( $_POST['pid'] ) );

                if ( !empty( $new_product_id ) )
                {
                    \update_option( $this->wc_am_product_id, $new_product_id );
                    $this->product_id = $new_product_id;
                }
            }
            if ( !$this->is_active() or $activation_status == '' or $api_key == '' or $api_key != $current_api_key )
            {
                if ( !empty( $current_api_key ) and ( $current_api_key != $api_key ) ) $this->replace_license_key( $current_api_key );
                $args = array
                (
                    'api_key' => $api_key,
                );
                $activate_results = \json_decode( $this->activate( $args ), true );
                if ( true === $activate_results['success'] and true === $activate_results['activated'] )
                {
                    $options[$this->data_instance_key]   = $this->wc_am_instance_id;
                    $options[$this->data_product_id_key] = $this->product_id;
                    $options[$this->data_version_key]    = $this->software_version;
                    $options[$this->data_activated_key]  = 'Activated';
                    $options[$this->wc_am_api_key_key]   = $api_key;
                    $options[$this->data_expiry_key]     = empty( $activate_results['data']['expiry_date'] ) ? '' : \date( 'Y-m-d', $activate_results['data']['expiry_date'] );
                    $options[$this->data_keep_key]       = $keep_license;
                    \update_option( $this->data_key, $options );
                    \update_option( $this->wc_am_activated_key, 'Activated' );
                    \delete_transient( MOLONGUI_AUTHORSHIP_PRO_PREFIX . '_deactivated_key_130' );
                    $result = "success";
                    $notice = __( "License activated.", 'molongui-authorship-pro' ) . ' ' . \esc_attr( "{$activate_results['message']}." );
                }
                if ( false == $activate_results and !empty( $this->data ) and !empty( $this->wc_am_activated_key ) )
                {
                    $options[$this->data_instance_key]   = $this->wc_am_instance_id;
                    $options[$this->data_product_id_key] = $this->product_id;
                    $options[$this->data_version_key]    = $this->software_version;
                    $options[$this->data_activated_key]  = 'Deactivated';
                    $options[$this->wc_am_api_key_key]   = '';
                    $options[$this->data_expiry_key]     = '';
                    $options[$this->data_keep_key]       = $keep_license;
                    \update_option( $this->data_key, $options );
                    \update_option( $this->wc_am_activated_key, 'Deactivated' );

                    $result = "error";
                    $notice = \esc_html__( "Connection failed to the Molongui Licensing Server. Try again later. There may be a problem on your server preventing outgoing requests, or the store is blocking your request to activate the plugin.", 'molongui-authorship-pro' );
                }
                if ( isset( $activate_results['data']['error_code'] ) and !empty( $this->data ) and !empty( $this->wc_am_activated_key ) )
                {
                    $options[$this->data_instance_key]   = $this->wc_am_instance_id;
                    $options[$this->data_product_id_key] = $this->product_id;
                    $options[$this->data_version_key]    = $this->software_version;
                    $options[$this->data_activated_key]  = 'Deactivated';
                    $options[$this->wc_am_api_key_key]   = '';
                    $options[$this->data_expiry_key]     = '';
                    $options[$this->data_keep_key]       = $keep_license;
                    \update_option( $this->data_key, $options );
                    \update_option( $this->wc_am_activated_key, 'Deactivated' );

                    $result = "error";
                    $notice = \esc_attr( "{$activate_results['data']['error']}" );
                }
                echo \json_encode( array( $result, $notice ) );
                \wp_die();
            }

            \wp_die();
        }
        public function replace_license_key( $current_api_key )
        {
            $args = array
            (
                'api_key' => $current_api_key,
            );
            $reset = $this->deactivate( $args );

            if ( $reset == true ) return true;

            \add_settings_error( 'not_deactivated_text', 'not_deactivated_error', \esc_html__( "The license could not be deactivated. Use the Deactivate button to manually deactivate your key before activating a new one. If all else fails, go to Plugins, then deactivate and reactivate this plugin, then go to the settings page for this plugin and enter your License information again to activate it. Also check the My Account dashboard to see if your key was still active for this site before the error message was displayed.", 'molongui-authorship-pro' ), 'updated' );

            return false;
        }
        public function deactivate_license_key()
        {
            if ( !\check_ajax_referer( 'mfw_license_nonce', 'nonce', false ) )
            {
                $result = "error";
                $notice = __( "Security token check failed. Please make sure you're deactivating the license from the plugin settings page. If you already are, please refresh the page.", 'molongui-authorship-pro' );
                echo \json_encode( array( $result, $notice ) );
                \wp_die();
            }

            $result = "error";
            $notice = __( "It is like there is no active license key to deactivate. Please log in to My Account dashboard to see if your key is still active for this site.", 'molongui-authorship-pro' );
            $args   = array
            (
                'api_key' => $this->data[$this->wc_am_api_key_key],
            );

            if ( $this->is_active() and $this->data[$this->wc_am_api_key_key] != '' )
            {
                $activate_results = \json_decode( $this->deactivate( $args ), true );
                if ( $activate_results['success'] === true and $activate_results['deactivated'] === true )
                {
                    if ( !empty( $this->wc_am_activated_key ) )
                    {
                        $clear = \array_merge( $this->data, array
                        (
                            $this->data_product_id_key => '',
                            $this->data_version_key    => '',
                            $this->data_activated_key  => 'Deactivated',
                            $this->wc_am_api_key_key   => '',
                            $this->data_expiry_key     => '',
                        ) );
                        \update_option( $this->data_key, $clear );
                        \update_option( $this->wc_am_activated_key, 'Deactivated' );
                        \update_option( $this->wc_am_product_id, '' );
                    }
                    $result = "success";
                    $notice = \esc_html__( "License key deactivated.", 'molongui-authorship-pro' ) . ' ' .  \esc_attr( "{$activate_results['activations_remaining']}." );
                }
                elseif ( isset( $activate_results['data']['error_code'] ) and !empty( $this->data ) and !empty( $this->wc_am_activated_key ) )
                {
                    $clear = \array_merge( $this->data, array
                    (
                        $this->data_product_id_key => '',
                        $this->data_version_key    => '',
                        $this->data_activated_key  => 'Deactivated',
                        $this->wc_am_api_key_key   => '',
                        $this->data_expiry_key     => '',
                    ) );
                    \update_option( $this->data_key, $clear );
                    \update_option( $this->wc_am_activated_key, 'Deactivated' );
                    \update_option( $this->wc_am_product_id, '' );
                    $result = "error";
                    $notice = \esc_attr( "{$activate_results['data']['error']}" );
                }
            }
            echo \json_encode( array( $result, $notice ) );
            \wp_die();
        }
        private function create_software_api_url( $args )
        {
            return \esc_url_raw( \add_query_arg( 'wc-api', 'wc-am-api', $this->api_url ) . '&' . \http_build_query( $args ) );
        }
        private function send_query( $args, $defaults = null )
        {
            $args       = \is_null( $defaults ) ? $args : \wp_parse_args( $defaults, $args );
            $target_url = $this->create_software_api_url( $args );
            $headers    = array( 'headers' => array( 'content-type' => 'application/json; charset=UTF-8' ) );
            $timeout    = array( 'timeout' => 30 );
            $request    = \wp_safe_remote_post( $target_url, \array_merge( $headers, $timeout ) );
            if ( \is_wp_error( $request ) or \wp_remote_retrieve_response_code( $request ) != 200 ) return false;
            $response = \wp_remote_retrieve_body( $request );
            return ( empty( $response ) and \is_null( $defaults ) ) ? false : $response;
        }

    } // class
} // class_exists