<?php
defined( 'ABSPATH' ) or exit;
add_action( 'authorship/init', function()
{
    authorship_debug( null, sprintf( "%s %s", MOLONGUI_AUTHORSHIP_TITLE, MOLONGUI_AUTHORSHIP_VERSION ) );
});
