<?php
defined( 'ABSPATH' ) or exit;
function authorship_dump( $data, $backtrace = false, $in_admin = true, $die = false )
{
    if ( apply_filters( 'authorship/disable_dump', false ) ) return;
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
        if ( !did_action( 'admin_notices' ) and !$die )
        {
            add_action( 'admin_notices', function() use ( $debug )
            {
                if ( !current_user_can( 'administrator' ) ) return;

                $html_message = '';
                if ( authorship_is_block_editor() and !did_action( '_authorship/hide_block_editor' ) )
                {
                    do_action( '_authorship/hide_block_editor' );

                    echo '<style>#editor{display:none} .wrap.hide-if-js.block-editor-no-js{display:block}</style>';
                    $html_message .= sprintf( '<div class="notice notice-warning" style="display: block !important; background: #dba6171f;"><h2>Hidden Block Editor</h2><pre>%s</pre></div>', __( 'The Block Editor has been hidden so debug information can be seen.', 'molongui-authorship' ) );
                }

                $html_message .= sprintf( '<div class="notice notice-info" style="display: block !important;"><h2>Debug Information</h2><pre>%s</pre></div>', $debug );
                echo $html_message;
            }, 0 );
        }
        else
        {
            if ( !current_user_can( 'administrator' ) ) return;

            if ( $die )
            {
                echo '<pre style="margin: 20px; padding: 1em; border: 2px dashed green; background: #fbfbfb;">';
            }
            else
            {
                echo '<pre style="margin: 20px 20px 20px 180px; padding: 1em; border: 2px dashed green; background: #fbfbfb;">';
            }
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
function authorship_dump_filter( $value )
{
    authorship_dump( $value );
    return $value;
}