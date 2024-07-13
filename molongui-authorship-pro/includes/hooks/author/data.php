<?php

use Molongui\Authorship\Author;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_filter_guest_link' ) )
{
    function authorship_pro_filter_guest_link( $default, $guest_id, $type, $author, $opt )
    {
        if ( 'guest' !== $type ) return $default;
        if ( !\is_object( $author ) and $guest_id )
        {
            $class  = new Author( $guest_id, 'guest' );
            $author = $class->get();
        }
        $permastruct = ( !empty( $opt['guest_archive_permalink'] ) ? '/' . $opt['guest_archive_permalink'] : '' ) .
                       ( !empty( $opt['guest_archive_base'] )      ? '/' . $opt['guest_archive_base'] : '/author' );
        $actual_url = \user_trailingslashit( \untrailingslashit( \authorship_home_url() ).$permastruct.'/'.$author->post_name );

        $url = $opt['guest_pages'] ? $actual_url : '#molongui-disabled-link';
        $url = apply_filters( 'authorship_pro/author/url', $url, $actual_url, $guest_id, $type, $author );

        return $url;
    }
    add_filter( 'authorship/author/url', 'authorship_pro_filter_guest_link', 10, 5 );
}