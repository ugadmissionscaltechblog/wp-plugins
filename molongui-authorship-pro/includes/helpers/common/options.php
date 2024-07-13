<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function _authorship_reset_options()
{
    $rc = true;
    $defaults = authorship_get_defaults();

    $r = update_option( MOLONGUI_AUTHORSHIP_PREFIX.'_options', $defaults );
    if ( !$r )
    {
        if ( $defaults !== get_option( MOLONGUI_AUTHORSHIP_PREFIX.'_options' ) and $defaults !== maybe_serialize( get_option( MOLONGUI_AUTHORSHIP_PREFIX.'_options' ) ) )
        {
            $rc = 'update';
        }
    }

    return $rc;
}