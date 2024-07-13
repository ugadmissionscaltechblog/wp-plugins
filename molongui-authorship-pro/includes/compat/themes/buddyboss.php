<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
add_filter( 'authorship_pro/sc/author_posts/atts', function( $atts, $attributes )
{
    if ( !empty( $attributes['author'] ) ) return $atts;
    if ( function_exists( 'bp_is_user' ) and !bp_is_user() ) return $atts;
    if ( function_exists( 'bp_displayed_user_id' ) )
    {
        $atts['author'] = esc_attr( bp_displayed_user_id() );
        $atts['guest']  = 'user';
    }
    return $atts;
}, 10, 2 );