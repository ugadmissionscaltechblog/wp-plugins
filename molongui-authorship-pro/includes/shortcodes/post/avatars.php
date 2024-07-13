<?php

use Molongui\Authorship\Author;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

add_shortcode( 'molongui_post_avatars', 'shortcode_authorship_post_avatars' );
add_shortcode( 'post_avatars', 'shortcode_authorship_post_avatars' );
if ( !function_exists( 'shortcode_authorship_post_avatars' ) )
{
    function shortcode_authorship_post_avatars( $atts )
    {
        $atts = shortcode_atts( array
        (
            'pid'             => null,
            'size'            => '',
            'context'         => 'screen',
            'source'          => 'local',
            'default'         => 'gravatar',
            'display_name'    => 'no',
            'link'            => 'yes',
            'link_class'      => '',
            'wrapper_tag'     => 'div',
            'wrapper_class'   => 'm-post-avatar',
            'container_tag'   => 'div',
            'container_id'    => '',
            'container_class' => 'm-post-avatars',
            'display_errors'  => 'yes',
        ), (array)$atts );
        $atts['pid']            = (int) $atts['pid'];
        $atts['context']        = ( in_array( strtolower( $atts['context'] ), array( 'screen', 'box', 'url' ) ) ? strtolower( $atts['context'] ) : '' );
        $atts['display_name']   = ( in_array( strtolower( $atts['display_name'] ), array( 'yes', 'true', 'on', 'show' ) ) ? true : false );
        $atts['link']           = ( in_array( strtolower( $atts['link'] ), array( 'yes', 'true', 'on', 'link' ) ) ? true : false );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        if ( strpos( $atts['size'], ',' ) !== false )
        {
            $no_whitespaces_size = preg_replace( '/\s*,\s*/', ',', strip_tags( $atts['size'] ) );
            $size_array = explode( ',', $no_whitespaces_size );
            $atts['size'] = array( $size_array[0], $size_array[1] );
        }
        $authors = authorship_get_post_authors( $atts['pid'] );
        if ( !$authors )
        {
            if ( empty( $atts['pid'] ) )
            {
                return sprintf( __( "%sThis shortcode requires you to provide a post ID when running outside The Loop.%s", 'molongui-authorship-pro' ), '<code><small>', '</small></code>' );
            }

            return sprintf( __( "%sProvided post ID is wrong. Make sure it is an integer for an existing post.%s", 'molongui-authorship-pro' ), '<code><small>', '</small></code>' );
        }
        $markup = '';
        foreach ( $authors as $data )
        {
            $author  = new Author( $data->id, $data->type );
            $name    = $atts['display_name'] ? ( $atts['link'] ? '<a href="'. $author->get_url() .'" class="'.$atts['link_class'].'">'.$author->get_name().'</a>' : $author->get_name() ) : '';

            $markup .= '<'.$atts['wrapper_tag'] .' class="'.$atts['wrapper_class'].'" data-author-id="'.$data->id.'" data-author-type="'.$data->type.'">';
            $markup .= $atts['link'] ? '<a href="'. $author->get_url() .'" class="'.$atts['link_class'].'">' : '';
            $markup .= $author->get_avatar( $atts['size'], $atts['context'], $atts['source'], $atts['default'] );
            $markup .= $atts['link'] ? '</a>' : '';
            $markup .= empty( $name ) ? '' : '<span class="m-post-avatar-name"> '.$name.'</span>';
            $markup .= '</'.$atts['wrapper_tag'].'>';
        }
        if ( !empty( $atts['container_tag'] ) )
        {
            $markup = '<'.$atts['container_tag'] .' id="'.$atts['container_id'].'" class="'.$atts['container_class'].'">'. $markup . '</'.$atts['container_tag'].'>';
        }
        return $markup;
    }
}