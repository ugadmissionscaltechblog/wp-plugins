<?php
defined( 'ABSPATH' ) or exit;
if ( !molongui_is_request( 'frontend' ) ) return;
if ( !function_exists( 'authorship_pro_change_author_template' ) )
{
    function authorship_pro_change_author_template( $template )
    {
        if ( is_author() )
        {
            $options  = authorship_get_options();
            $filename = !empty( $options['user_archive_tmpl'] ) ? trim( $options['user_archive_tmpl'] ) : '';
            if ( empty( $filename ) or 'php' !== pathinfo( $filename, PATHINFO_EXTENSION ) ) return $template;
            if ( is_file( $filename ) )
            {
                return $filename;
            }
            else
            {
                $new_template = locate_template( array( $filename ) );
                if ( !empty( $new_template ) ) return $new_template;
            }
        }
        return $template;
    }
    add_filter( 'template_include', 'authorship_pro_change_author_template', 99 );
}