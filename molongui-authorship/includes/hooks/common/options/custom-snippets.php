<?php
defined( 'ABSPATH' ) or exit;
add_action( 'plugins_loaded', function()
{
    if ( is_admin() and apply_filters( 'authorship/disable_custom_php_on_admin', '__return_true' ) )
    {
        return;
    }

    $options = authorship_get_options();

    if ( !empty( $options['custom_php'] ) )
    {
        $tag = '<?php';
        if ( 0 === strpos( $options['custom_php'], $tag ) )
        {
            $options['custom_php'] = substr( $options['custom_php'], strlen( $tag ) );
        }

        eval( $options['custom_php'] );
        authorship_debug( null, __( "Custom PHP snippets loaded." ) );
    }
}, PHP_INT_MAX );