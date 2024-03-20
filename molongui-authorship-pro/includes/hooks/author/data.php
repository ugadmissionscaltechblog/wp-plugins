<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
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
if ( !function_exists( 'authorship_pro_filter_author_bio' ) )
{
    function authorship_pro_filter_author_bio( $bio, $author_id, $author_type, $author )
    {
        if ( apply_filters( '_authorship/doing_shortcode/author_bio', false ) ) return $bio;
        $options = apply_filters( '_authorship/get_options', authorship_get_options() );
        if ( 'short' !== $options['author_box_bio_source'] or \is_author() or \is_guest_author() ) return $bio;
        $short_bio = authorship_pro_get_author_short_bio( $author_id, $author_type, $author );
        $short_bio = apply_filters( 'authorship_pro/author/short_bio', $short_bio, $bio, $author_id, $author_type, $author );
        return $short_bio ? $short_bio : $bio;
    }
    add_filter( 'authorship/author/bio', 'authorship_pro_filter_author_bio', 10, 4 );
}