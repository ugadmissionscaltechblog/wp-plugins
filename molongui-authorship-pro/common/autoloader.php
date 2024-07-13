<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

spl_autoload_register( 'authorship_pro_autoload' );
function authorship_pro_autoload( $class_name )
{
    $class_name       = trim( $class_name, '\\' );
    $namespace_prefix = trim( MOLONGUI_AUTHORSHIP_PRO_NAMESPACE, '\\' );
    if ( false === strpos( $class_name, $namespace_prefix.'\\' ) )
    {
        return;
    }

    $file_name = '';
    $namespace = '';
    $includes  = true;
    $_class_name = trim( str_replace( $namespace_prefix, '', $class_name ), '\\' );
    $path_parts = explode( '\\', $_class_name );
    for ( $i = count( $path_parts ) - 1; $i >= 0; $i-- )
    {
        if ( 'Common' === $path_parts[$i] )
        {
            $includes = false;
        }
        $current = strtolower( $path_parts[$i] );
        $current = str_ireplace( '_', '-', $current );
        if ( count( $path_parts ) - 1 === $i )
        {
            $file_name = $current . '.php';
        }
        else
        {
            $namespace = $current . '/' . $namespace;
        }
    }
    if ( $includes )
    {
        $filepath = MOLONGUI_AUTHORSHIP_PRO_DIR . 'includes/' . $namespace . $file_name;
    }
    else
    {
        $filepath = MOLONGUI_AUTHORSHIP_PRO_DIR . $namespace . $file_name;
    }
    if ( file_exists( $filepath ) )
    {
        require_once $filepath;
    }
    else
    {

        wp_die( esc_html( "The file attempting to be loaded at $filepath does not exist." ) );
    }
}