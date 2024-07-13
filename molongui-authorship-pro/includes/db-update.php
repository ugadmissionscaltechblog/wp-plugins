<?php

namespace Molongui\Authorship\Pro;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class DB_Update
{
	public function db_update_6()
	{
        $license = \get_option( 'molongui_authorship_pro_license' );

        $license['status']       = 'Activated' == $license['status'] ? 'wc-active' : '';
        $license['purchase']     = '';
        $license['product_type'] = \in_array( \get_option( 'molongui_authorship_pro_product_id' ), array( 9141, 9142, 9143 ) ) ? 'subscription' : 'one-time';
        unset ( $license['expiry'] );

        \update_option( 'molongui_authorship_pro_license', $license );
    }
	public function db_update_5()
	{
        $now = \get_option( 'molongui_authorship_pro_install' );
        $new = array
        (
            'timestamp' => $now['install_date'],
            'version'   => $now['install_version'],
        );
        \update_option( 'molongui_authorship_pro_install', $new );
    }
	public function db_update_4()
	{
        \update_option( 'molongui_authorship_pro_flush_rewrite_rules', 0, true );
    }
	public function db_update_3()
	{
        $current = \get_option( 'molongui_authorship_pro_license' );
        $reset = array
        (
            'instance'   => '',
            'product_id' => '',
            'status'     => 'Deactivated',
            'key'        => '',
            'expiry'     => isset( $current['expiration_date'] ) ? $current['expiration_date'] : '',
            'keep'       => isset( $current['keep_license'] ) ? $current['keep_license'] : 1,
            'version'    => '1.3.0',
        );
        \update_option( 'molongui_authorship_pro_license', $reset );
        \update_option( 'molongui_authorship_pro_product_id', '' );
        \update_option( 'molongui_authorship_pro_activated', 'Deactivated' );
        \delete_option( 'molongui_authorship_pro_version' );
        \set_transient( 'molongui_authorship_pro'.'_deactivated_key_130', 1 );
    }
	public function db_update_2()
	{
		$value = \get_option( 'molongui_authorship_contributors_page' );
        \delete_option( 'molongui_authorship_contributors_page' );
        \update_option( 'molongui_authorship_pro_contributors_page_id', $value );
        $value = \get_option( 'molongui_authorship_license' );
        \delete_option( 'molongui_authorship_license' );
        \update_option( 'molongui_authorship_pro_license', $value );
        $value = \get_option( 'molongui_authorship_product_id' );
        \delete_option( 'molongui_authorship_product_id' );
        \update_option( 'molongui_authorship_pro_product_id', $value );
        $value = \get_option( 'molongui_authorship_instance' );
        \delete_option( 'molongui_authorship_instance' );
        \update_option( 'molongui_authorship_pro_instance', $value );
        $value = \get_option( 'molongui_authorship_activated' );
        \delete_option( 'molongui_authorship_activated' );
        \update_option( 'molongui_authorship_pro_activated', $value );
        \update_option( 'molongui_authorship_pro_version', '1.0.1', 'no' );
    }

} // class