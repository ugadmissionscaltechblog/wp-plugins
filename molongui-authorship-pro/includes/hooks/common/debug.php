<?php
defined( 'ABSPATH' ) or exit;
add_action( 'authorship_pro/init', function()
{
    authorship_debug( null, sprintf( "%s %s", MOLONGUI_AUTHORSHIP_PRO_TITLE, MOLONGUI_AUTHORSHIP_PRO_VERSION ) );
});
