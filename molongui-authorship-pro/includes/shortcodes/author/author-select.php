<?php
defined( 'ABSPATH' ) or exit;

add_shortcode( 'molongui_author_select', 'authorship_pro_author_select_shortcode' );
if ( !function_exists( 'authorship_pro_author_select_shortcode' ) )
{
    function authorship_pro_author_select_shortcode( $atts )
    {
        if ( !is_array( $atts ) )
        {
            $atts = array();
        }
        $atts['output']   = 'select';
        $atts['dev_mode'] = 'on';
        return authorship_pro_author_list_shortcode( $atts );
    }
}