<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
spl_autoload_register( 'authorship_pro_autoload' );
function authorship_pro_autoload( $class_name )
{
    $namespace_prefix = 'Molongui\Authorship\Pro\\';
    if ( false === strpos( $class_name, $namespace_prefix ) ) return;
    $file_parts = explode( '\\', rtrim( $class_name, '\\' ) );
    $path_parts = array_slice( $file_parts, count( explode( '\\', rtrim( $namespace_prefix, '\\' ) ) ) );
    $namespace = '';
    for ( $i = count( $path_parts ) - 1; $i >= 0; $i-- )
    {
        $current = strtolower( $path_parts[ $i ] );
        $current = str_ireplace( '_', '-', $current );
        if ( count( $path_parts ) - 1 === $i )
        {
            $file_name = $current.'.php';
        }
        else $namespace = '/' . $current . $namespace;
    }
    $filepath = trailingslashit( untrailingslashit( MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/' ) . $namespace ) . $file_name;
    if ( file_exists( $filepath ) )
    {
        require_once $filepath;
    }
    else
    {

        wp_die( esc_html( "The file attempting to be loaded at $filepath does not exist." ) );
    }
}
