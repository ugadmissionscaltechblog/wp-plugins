<?php
defined( 'ABSPATH' ) or exit;

if ( !function_exists( 'authorship_pro_load_user_roles' ) )
{
    function authorship_pro_load_user_roles( $default_roles )
    {
        $options       = authorship_get_options();
        $options_key   = 'user_roles';
        $enabled_roles = explode( ",", $options[$options_key] );
        if ( empty( $enabled_roles ) ) return $default_roles;

        return $enabled_roles;
    }
    add_filter( 'authorship/user/roles', 'authorship_pro_load_user_roles' );
}