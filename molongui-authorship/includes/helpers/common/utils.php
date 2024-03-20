<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( '__return_list_true' ) )
{
    function __return_list_true() // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
    {
        return array( true, null );
    }
}
if ( !function_exists( '__return_list_false' ) )
{
    function __return_list_false() // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
    {
        return array( false, null );
    }
}
if ( !function_exists( '__return_original' ) )
{
    function __return_original( $null, $original ) // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
    {
        return $original;
    }
}
function authorship_space_to_nbsp( $string )
{
    return str_replace( ' ', '&nbsp;', $string );
}