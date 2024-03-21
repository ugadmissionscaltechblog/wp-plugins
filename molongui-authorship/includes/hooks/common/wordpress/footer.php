<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_admin_footer' ) )
{
    function authorship_admin_footer( $footer_text )
    {
        global $current_screen;
        if ( $current_screen->id == 'molongui_page_' . MOLONGUI_AUTHORSHIP_NAME )
        {
            return ( sprintf( __( "If you like <strong>%s</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thank you in advance!", 'molongui-authorship' ),
                MOLONGUI_AUTHORSHIP_TITLE,
                '<a href="https://wordpress.org/support/view/plugin-reviews/' . MOLONGUI_AUTHORSHIP_NAME . '?filter=5#postform" target="_blank" class="molongui-admin-footer-link" data-rated="' . esc_attr__( "Thanks :)", 'molongui-authorship' ) . '">',
                '</a>' )
            );
        }
        return $footer_text;
    }
    add_filter( 'admin_footer_text', 'authorship_admin_footer', 999 );
}