<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_register_style( $file, $scope, $deps = array() )
{
    if ( empty( $file ) or empty( $scope ) ) return;
    if ( file_exists( trailingslashit( WP_PLUGIN_DIR ) . $file ) )
    {
        $handle   = MOLONGUI_AUTHORSHIP_PRO_NAME . '-' . str_replace( '_', '-', $scope );
        $function = 'authorship_pro_'.$scope.'_extra_styles';
        if ( function_exists( $function ) ) $extra = call_user_func( $function );

        wp_register_style( $handle, plugins_url( '/' ) . $file, $deps, MOLONGUI_AUTHORSHIP_PRO_VERSION, 'all' );
        if ( !empty( $extra ) ) wp_add_inline_style( $handle, $extra );
    }
}
function authorship_pro_enqueue_style( $file, $scope, $admin = false )
{
    if ( empty( $file ) or empty( $scope ) ) return;

    $filepath = trailingslashit( WP_PLUGIN_DIR ) . $file;

    if ( file_exists( $filepath ) )
    {
        $filesize = filesize( $filepath );
        if ( !$filesize ) return;

        $handle = MOLONGUI_AUTHORSHIP_PRO_NAME . '-' . str_replace( '_', '-', $scope );
        $inline = apply_filters( "authorship_pro/{$scope}/inline_styles", $filesize < 4096 );
        if ( $inline )
        {
            /*! This action is documented in includes/helpers/assets/styles.php */
            if ( !did_action( "_authorship_pro/{$scope}/styles_loaded" ) )
            {
                $hook = $admin ? 'admin_print_footer_scripts' : 'wp_print_footer_scripts';

                add_action( $hook, function() use ( $scope, $filepath, $handle )
                {
                    /*!
                     * PRIVATE FILTER HOOK.
                     *
                     * For internal use only. Not intended to be used by plugin or theme developers.
                     * Future compatibility NOT guaranteed.
                     *
                     * Please do not rely on this hook for your custom code to work. As a private hook it is meant to be
                     * used only by Molongui. It may be edited, renamed or removed from future releases without prior
                     * notice or deprecation phase.
                     *
                     * If you choose to ignore this notice and use this filter, please note that you do so at on your
                     * own risk and knowing that it could cause code failure.
                     */
                    $contents = apply_filters( "_authorship_pro/{$scope}/styles_contents", file_get_contents( $filepath ), $filepath );
                    $extra    = '';
                    $function = 'authorship_pro_'.$scope.'_extra_styles';
                    if ( function_exists( $function ) ) $extra = call_user_func( $function );

                    echo '<style id="'.$handle.'-inline-css" type="text/css" data-file="' . basename( $filepath ) . '">' . $contents . $extra . '</style>';
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
                do_action( "_authorship_pro/{$scope}/styles_loaded" );
            }
        }
        else
        {
            wp_enqueue_style( $handle );
        }
    }
}