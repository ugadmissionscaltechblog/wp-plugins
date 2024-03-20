<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'molongui_get_plugin_settings' ) )
{
    function molongui_get_plugin_settings( $id = '', $names = '' )
    {
        if ( empty( $id ) or empty( $names ) ) return;
        $settings = array();
        if ( is_array( $names ) ) foreach ( $names as $name ) $settings = array_merge( $settings, (array) get_option( molongui_get_constant( $id, $name.'_SETTINGS' ) ) );
        else $settings = get_option( molongui_get_constant( $id, $names.'_SETTINGS' ) );
        return $settings;
    }
}
if ( !function_exists( 'molongui_is_active' ) )
{
    function molongui_is_active( $plugin_dir )
    {
        return authorship_pro_is_active();
    }
}
if ( !function_exists( 'molongui_debug' ) )
{
    function molongui_debug( $data, $backtrace = false, $in_admin = true, $die = false )
    {
        if ( molongui_is_request( 'ajax' ) or molongui_is_request( 'api' ) or wp_is_json_request() ) return;
        if ( !$in_admin and is_admin() ) return;

        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 );
        $debug = array
        (
            'file'     => ( isset( $dbt[0]['file'] )     ? $dbt[0]['file'] : '' ),
            'line'     => ( isset( $dbt[0]['line'] )     ? $dbt[0]['line'] : '' ),
            'class'    => ( isset( $dbt[1]['class'] )    ? $dbt[1]['class'] : '' ),
            'function' => ( isset( $dbt[1]['function'] ) ? $dbt[1]['function'] : '' ),
        );

        if ( $backtrace )
        {
            $debug['filter']      = current_filter();
            $debug['is_admin']    = molongui_is_request( 'admin' );
            $debug['is_front']    = molongui_is_request( 'front' );
            $debug['is_ajax']     = molongui_is_request( 'ajax'  );
            $debug['is_cron']     = molongui_is_request( 'cron'  );
            $debug['in_the_loop'] = in_the_loop();
            $debug['backtrace']   = wp_debug_backtrace_summary( null, 0, false );
        }

        $debug['data'] = $data;
        $debug = print_r( $debug, true );
        if ( is_admin() )
        {
            if ( !did_action( 'admin_notices' ) )
            {
                add_action( 'admin_notices', function() use ( $debug )
                {
                    if ( !current_user_can( 'administrator' ) ) return;

                    $html_message = sprintf( '<div class="notice notice-info" style="display: block !important;"><h2>Debug Information</h2><pre>%s</pre></div>', $debug );
                    echo $html_message;
                }, 0 );
            }
            else
            {
                if ( !current_user_can( 'administrator' ) ) return;

                echo '<pre style="margin: 20px 20px 20px 180px; padding: 1em; border: 2px dashed green; background: #fbfbfb;">';
                echo $debug;
                echo "</pre>";
            }
        }
        else
        {
            if ( function_exists( 'is_user_logged_in' ) and function_exists( 'current_user_can' ) )
            {
                if ( !is_user_logged_in() or !current_user_can( 'administrator' ) ) return;
            }

            echo '<pre style="margin: 1em; padding: 1em; border: 2px dashed green; background: #fbfbfb;">';
            echo $debug;
            echo "</pre>";
        }
        if ( $die ) die;
    }
}
if ( !function_exists( 'molongui_debug_filter' ) )
{
    function molongui_debug_filter( $value )
    {
        molongui_debug( $value );
        return $value;
    }
}