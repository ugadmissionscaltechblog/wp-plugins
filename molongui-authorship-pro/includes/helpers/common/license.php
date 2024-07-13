<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_is_active()
{
    return did_action( 'authorship_pro/init' );
}