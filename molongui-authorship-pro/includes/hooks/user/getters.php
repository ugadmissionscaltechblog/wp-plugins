<?php
use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit;
function authorship_pro_load_user_roles( $value )
{
    $user_roles = Settings::get( 'user_roles', "administrator,editor,author,contributor" );

    if ( empty( $user_roles ) )
    {
        return array('');
    }

    return explode( ",", $user_roles );
}
add_filter( 'authorship/user/roles', 'authorship_pro_load_user_roles' );