<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_is_active()
{
    return did_action( 'authorship_pro/init' );
}