<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_guest_page_template' ) )
{
    function authorship_pro_guest_page_template( $template )
    {
        global $wp_query;
        if ( !is_guest_author() ) return $template;
        if ( $wp_query->is_404 and !isset( $wp_query->guest_author_id ) )
        {

            return $template;
        }
        $wp_query->is_404 = false;
        if ( array_key_exists( 'guest-author-name', $wp_query->query_vars ) )
        {
            $options  = Settings::get();
            $filename = !empty( $options['guest_archive_tmpl'] ) ? trim( $options['guest_archive_tmpl'] ) : '';
            if ( empty( $filename ) or 'php' !== pathinfo( $filename, PATHINFO_EXTENSION ) ) return $template;
            if ( is_file( $filename ) )
            {
                return $filename;
            }
            else
            {
                $new_template = locate_template( array_filter( array( $filename, 'author.php', 'archive.php', 'index.php' ) ) );
                if ( !empty( $new_template ) ) return $new_template;
            }
        }
        return $template;
    }
    add_filter( 'template_include', 'authorship_pro_guest_page_template', PHP_INT_MAX );
}
if ( !function_exists( 'authorship_pro_guest_page_title' ) )
{
    function authorship_pro_guest_page_title( $title )
    {
        if ( $author = get_query_var( 'guest-author-name', 0 ) )
        {
            $guest = molongui_get_author_by( 'name', $author, 'guest', false );
            if ( $guest ) $title['title'] = $guest->post_title;
        }

        return $title;
    }
    add_filter( 'document_title_parts', 'authorship_pro_guest_page_title' );
}
if ( !function_exists( 'authorship_pro_guest_page_body_class' ) )
{
    function authorship_pro_guest_page_body_class( $classes )
    {
        global $wp_query;
        if ( isset( $wp_query->guest_author_id ) )
        {
            $classes[] = 'archive';
            $classes[] = 'author';
            $classes[] = 'guest-author';
            $classes[] = 'author-' . get_query_var( 'guest-author-name', 0 );
            $classes[] = 'molongui-author-page';
        }

        return $classes;
    }
    add_filter( 'body_class', 'authorship_pro_guest_page_body_class' );
}