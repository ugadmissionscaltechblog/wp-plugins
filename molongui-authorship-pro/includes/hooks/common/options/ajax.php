<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_import_options()
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
                    $value = array_merge( authorship_get_defaults(), $value );
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
add_action( 'wp_ajax_'.MOLONGUI_AUTHORSHIP_PREFIX.'_import_options', 'authorship_pro_import_options' );
function authorship_pro_reset_options()
{
    check_ajax_referer( 'mfw_reset_options_nonce', 'nonce', true );
    $plugin_id = $_POST['id'];
    $rc = _authorship_reset_options();
    echo $rc;
    wp_die();
}
add_action( 'wp_ajax_'.MOLONGUI_AUTHORSHIP_PREFIX.'_reset_options' , 'authorship_pro_reset_options' );
function authorship_pro_restart_plugin()
{
    check_ajax_referer( 'mfw_reset_options_nonce', 'nonce', true );
    $plugin_id = $_POST['id'];
    $r = molongui_restart_plugin( MOLONGUI_AUTHORSHIP_BASENAME );
    wp_die();
}
