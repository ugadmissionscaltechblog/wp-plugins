<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;

add_shortcode( 'molongui_author_name',   'shortcode_molongui_author_name'   );
add_shortcode( 'molongui_author_slug',   'shortcode_molongui_author_slug'   );
add_shortcode( 'molongui_author_url',    'shortcode_molongui_author_url'    );
add_shortcode( 'molongui_author_bio',    'shortcode_molongui_author_bio'    );
add_shortcode( 'molongui_author_mail',   'shortcode_molongui_author_mail'   );
add_shortcode( 'molongui_author_meta',   'shortcode_molongui_author_meta'   );
add_shortcode( 'molongui_author_link',   'shortcode_molongui_author_link'   );
add_shortcode( 'molongui_author_avatar', 'shortcode_molongui_author_avatar' );
if ( !function_exists( 'shortcode_molongui_author_name' ) )
{
    function shortcode_molongui_author_name( $atts )
    {
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_name', '__return_true' );
        $atts = shortcode_atts( array
        (
            'id'             => null,
            'type'           => '',
            'display_errors' => 'yes',
        ), array_map( 'strtolower', (array)$atts ) );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        if ( !empty( $atts['id'] ) and !empty( $atts['type'] ) ) $validate = false;
        else $validate = true;
        $author = new Author( $atts['id'], $atts['type'], null, $atts['display_errors'] );
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_name', '__return_true' );

        return $author->get_name();
    }
}
if ( !function_exists( 'shortcode_molongui_author_slug' ) )
{
    function shortcode_molongui_author_slug( $atts )
    {
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_slug', '__return_true' );
        $atts = shortcode_atts( array
        (
            'id'             => null,
            'type'           => '',
            'display_errors' => 'yes',
        ), array_map( 'strtolower', (array)$atts ) );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        if ( !empty( $atts['id'] ) and !empty( $atts['type'] ) ) $validate = false;
        else $validate = true;
        $author = new Author( $atts['id'], $atts['type'], null, $atts['display_errors'] );
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_slug', '__return_true' );

        return $author->get_slug();
    }
}
if ( !function_exists( 'shortcode_molongui_author_url' ) )
{
    function shortcode_molongui_author_url( $atts )
    {
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_url', '__return_true' );
        $atts = shortcode_atts( array
        (
            'id'             => null,
            'type'           => '',
            'display_errors' => 'yes',
        ), array_map( 'strtolower', (array)$atts ) );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        if ( !empty( $atts['id'] ) and !empty( $atts['type'] ) ) $validate = false;
        else $validate = true;
        $author = new Author( $atts['id'], $atts['type'], null, $atts['display_errors'] );
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_url', '__return_true' );

        return $author->get_url();
    }
}
if ( !function_exists( 'shortcode_molongui_author_bio' ) )
{
    function shortcode_molongui_author_bio( $atts )
    {
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_bio', '__return_true' );
        $atts = shortcode_atts( array
        (
            'id'             => null,
            'type'           => '',
            'length'         => 'long',
            'display_errors' => 'yes',
        ), array_map( 'strtolower', (array)$atts ) );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        if ( !empty( $atts['id'] ) and !empty( $atts['type'] ) ) $validate = false;
        else $validate = true;
        $author = new Author( $atts['id'], $atts['type'], null, $atts['display_errors'] );
        switch ( $atts['length'] )
        {
            case 'short':
                $bio = $author->get_meta( 'short_bio' );
            break;

            case 'long' :
            default:
                $bio = $author->get_bio();
            break;
        }
        add_filter( 'authorship_pro/author_bio', 'do_blocks', 9 );
        add_filter( 'authorship_pro/author_bio', 'wptexturize' );
        add_filter( 'authorship_pro/author_bio', 'convert_smilies', 20 );
        add_filter( 'authorship_pro/author_bio', 'wpautop' );
        add_filter( 'authorship_pro/author_bio', 'shortcode_unautop' );
        add_filter( 'authorship_pro/author_bio', 'prepend_attachment' );
        add_filter( 'authorship_pro/author_bio', 'wp_filter_content_tags' );
        add_filter( 'authorship_pro/author_bio', 'do_shortcode', 11 ); // AFTER wpautop().
        $bio = $GLOBALS['wp_embed']->autoembed( $bio );

        $bio = apply_filters( 'authorship_pro/author_bio', $bio );
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_bio', '__return_true' );

        return $bio;
    }
}
if ( !function_exists( 'shortcode_molongui_author_mail' ) )
{
    function shortcode_molongui_author_mail( $atts )
    {
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_mail', '__return_true' );
        $atts = shortcode_atts( array
        (
            'id'             => null,
            'type'           => '',
            'display_errors' => 'yes',
        ), array_map( 'strtolower', (array)$atts ) );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        if ( !empty( $atts['id'] ) and !empty( $atts['type'] ) ) $validate = false;
        else $validate = true;
        $author = new Author( $atts['id'], $atts['type'], null, $atts['display_errors'] );
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_mail', '__return_true' );

        return $author->get_mail();
    }
}
if ( !function_exists( 'shortcode_molongui_author_meta' ) )
{
    function shortcode_molongui_author_meta( $atts )
    {
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_meta', '__return_true' );
        $atts = shortcode_atts( array
        (
            'id'             => null,
            'type'           => '',
            'key'            => '',
            'display_errors' => 'yes',
        ), array_map( 'strtolower', (array)$atts ) );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        if ( !empty( $atts['id'] ) and !empty( $atts['type'] ) ) $validate = false;
        else $validate = true;
        $author = new Author( $atts['id'], $atts['type'], null, $atts['display_errors'] );
        if ( $atts['type'] == 'guest' and $atts['key'] == 'long_bio' )
        {
            $meta = $author->get_bio();
        }
        else
        {
            $meta = $author->get_meta( $atts['key'] );
        }
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_meta', '__return_true' );

        return $meta;
    }
}
if ( !function_exists( 'shortcode_molongui_author_link' ) )
{
    function shortcode_molongui_author_link( $atts )
    {
        if ( empty( $atts['key'] ) ) return '<code class="m-a-warning">'.__( "You need to provide a key to retrieve in order this shortcode to work.", 'molongui-authorship-pro' ).'</code>';
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_link', '__return_true' );
        $atts = shortcode_atts( array
        (
            'id'             => null,
            'type'           => '',
            'key'            => '',
            'text'           => '',
            'display_errors' => 'no',
        ), (array)$atts );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        $atts['text'] = empty( $atts['text'] ) ? ucfirst( $atts['key'] ) : $atts['text'];
        if ( !empty( $atts['id'] ) and !empty( $atts['type'] ) ) $validate = false;
        else $validate = true;
        $author = new Author( $atts['id'], $atts['type'], null, $atts['display_errors'] );
        $meta = $author->get_meta( $atts['key'] );
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_link', '__return_true' );
        if ( !filter_var( $meta, FILTER_VALIDATE_URL ) ) return '<code class="m-a-warning">'.__( "Sorry, provided key does not have a valid URL value.", 'molongui-authorship-pro' ).'</code>';

        return '<a href="'.$meta.'">'.$atts['text'].'</a>';
    }
}
if ( !function_exists( 'shortcode_molongui_author_avatar' ) )
{
    function shortcode_molongui_author_avatar( $atts )
    {
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_avatar', '__return_true' );
        if ( isset( $atts['author'] ) ) $atts['id']   = $atts['author'];                                                // Prior to version 3.1.8
        if ( isset( $atts['guest']  ) ) $atts['type'] = ( strtolower( $atts['guest'] ) == 'yes' ? 'guest' : 'user' );   // Prior to version 3.1.8
        $atts = shortcode_atts( array
        (
            'id'             => null,
            'type'           => '',
            'size'           => 'full',
            'width'          => '',
            'height'         => '',
            'context'        => 'screen',
            'default'        => 'gravatar',
            'display_errors' => 'yes',
        ), array_map( 'strtolower', (array)$atts ) );
        $atts['display_errors'] = ( in_array( strtolower( $atts['display_errors'] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false );
        if ( !empty( $atts['width'] ) or !empty( $atts['height'] ) )
        {
            if ( !empty( $atts['width'] ) and empty( $atts['height'] ) ) $atts['height'] = $atts['width'];
            elseif ( empty( $atts['width'] ) and !empty( $atts['height'] ) ) $atts['width'] = $atts['height'];
            $atts['size'] = array( $atts['width'], $atts['height'] );
        }
        if ( !empty( $atts['id'] ) and !empty( $atts['type'] ) ) $validate = false;
        else $validate = true;
        $author = new Author( $atts['id'], $atts['type'], null, $atts['display_errors'] );
        $avatar = $author->get_avatar( $atts['size'], $atts['context'], null, $atts['default'] );
        if ( empty( $avatar ) and $atts['display_errors'] )
        {
            $avatar = '<code class="m-a-warning">'.sprintf( __( "No avatar available for %s with id %s.", 'molongui-authorship-pro' ), ( $atts['type'] == 'guest' ? __( "guest author", 'molongui-authorship-pro' ) : __( "user", 'molongui-authorship-pro' ) ), $atts['id'] ).'</code>';
        }
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_avatar', '__return_true' );

        return $avatar;
    }
}