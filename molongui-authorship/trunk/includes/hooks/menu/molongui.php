<?php
defined( 'ABSPATH' ) or exit;

$options = authorship_get_options();
add_action( 'init', function()
{
    $options = authorship_get_options();

    if ( !$options['molongui_menu'] )
    {
        $r = remove_action( 'admin_menu', 'authorship_admin_menu' );
    }
});