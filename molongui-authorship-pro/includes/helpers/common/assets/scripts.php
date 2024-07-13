<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_register_script( $file, $scope, $deps = array( 'jquery' ) )
{
    if ( empty( $file ) or empty( $scope ) ) return;
    if ( file_exists( trailingslashit( WP_PLUGIN_DIR ) . $file ) )
    {
        do_action( "authorship_pro/{$scope}/pre_register_script", $scope );

        $handle   = MOLONGUI_AUTHORSHIP_PRO_NAME . '-' . str_replace( '_', '-', $scope );
        $function = 'authorship_pro_'.$scope.'_script_params';
        if ( function_exists( $function ) ) $params = call_user_func( $function );

        wp_register_script( $handle, plugins_url( '/' ).$file, $deps, MOLONGUI_AUTHORSHIP_PRO_VERSION, true );
        if ( !empty( $params ) ) wp_localize_script( $handle, str_replace( '-', '_', $handle ).'_params', $params );
        do_action( "authorship_pro/{$scope}/post_register_script", $scope );
    }
}
function authorship_pro_enqueue_script( $file, $scope, $admin = false )
{
    if ( empty( $file ) or empty( $scope ) ) return;

    $filepath = trailingslashit( WP_PLUGIN_DIR ) . $file;

    if ( file_exists( $filepath ) )
    {
        $filesize = filesize( $filepath );
        if ( !$filesize ) return;

        $handle = MOLONGUI_AUTHORSHIP_PRO_NAME . '-' . str_replace( '_', '-', $scope );
        $inline = apply_filters( "authorship_pro/{$scope}/inline_script", $filesize < 4096 );
        if ( $inline )
        {
            /*! This action is documented in includes/helpers/assets/scripts.php */
            if ( !did_action( "_authorship_pro/{$scope}/script_loaded" ) )
            {
                $hook = $admin ? 'admin_print_footer_scripts' : 'wp_print_footer_scripts';
                do_action( "authorship_pro/{$scope}/pre_inline_script", $scope, $hook, $filepath );

                add_action( $hook, function() use ( $scope, $filepath, $handle )
                {
                    $contents = file_get_contents( $filepath );
                    $function = 'authorship_pro_'.$scope.'_script_params';
                    if ( function_exists( $function ) ) $params = call_user_func( $function );

                    if ( !empty( $params ) ) echo '<script id="'.$handle.'-inline-js-extra">' . 'var '.str_replace( '-', '_', $handle ).'_params'.' = '.json_encode( $params ).';' . '</script>';
                    echo '<script id="'.$handle.'-inline-js" type="text/javascript" data-file="'.basename( $filepath ).'">' . $contents . '</script>';
                });

                /*!
                 * PRIVATE ACTION HOOK.
                 *
                 * For internal use only. Not intended to be used by plugin or theme developers.
                 * Future compatibility NOT guaranteed.
                 *
                 * Please do not rely on this hook for your custom code to work. As a private hook it is meant to be
                 * used only by Molongui. It may be edited, renamed or removed from future releases without prior notice
                 * or deprecation phase.
                 *
                 * If you choose to ignore this notice and use this filter, please note that you do so at on your own
                 * risk and knowing that it could cause code failure.
                 */
                do_action( "_authorship_pro/{$scope}/script_loaded" );
            }
        }
        else
        {
            do_action( "authorship_pro/{$scope}/pre_enqueue_script", $scope );

            wp_enqueue_script( $handle );
            do_action( "authorship_pro/{$scope}/post_enqueue_script", $scope );
        }
    }
}