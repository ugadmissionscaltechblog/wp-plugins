<?php
defined( 'ABSPATH' ) or exit;

add_shortcode( 'molongui_byline', 'shortcode_authorship_post_byline' );
add_shortcode( 'post_byline', 'shortcode_authorship_post_byline' );
if ( !function_exists( 'shortcode_authorship_post_byline' ) )
{
    function shortcode_authorship_post_byline( $atts )
    {
        if ( !apply_filters( 'authorship/template_tags', true ) )
        {
            return '<code class="m-a-warning">'.__( "You have plugin template tags disabled. Probably using the 'authorship/template_tags' filter. Remove that filter to get this shortcode working.", 'molongui-authorship-pro' ).'</code>';
        }
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/post_byline', '__return_true' );
        $atts = shortcode_atts( array
        (
            'pid'            => null,
            'separator'      => ', ',
            'last_separator' => '',
            'before'         => '',
            'after'          => '',
            'linked'         => 'yes',
            'html_tag'       => '',
            'html_id'        => '',
            'html_class'     => '',
        ), (array)$atts );
        if ( is_null( $atts['pid'] ) or !is_numeric( $atts['pid'] ) or !$atts['pid'] )
        {
            global $post;
            if ( !empty( $post ) ) $atts['pid'] = $post->ID;
            $atts['pid'] = apply_filters( 'authorship_pro/sc/byline/post_id', $atts['pid'] );
        }
        else $atts['pid'] = (int) $atts['pid'];
        $byline = '';
        if ( strtolower( $atts['linked'] ) == 'yes' )
        {
            $byline = get_the_molongui_author_posts_link( $atts['pid'], $atts['separator'], $atts['last_separator'], $atts['before'], $atts['after'] );
        }
        else
        {
            $byline = get_the_molongui_author( $atts['pid'], $atts['separator'], $atts['last_separator'], $atts['before'], $atts['after'] );
        }
        if ( !empty( $atts['html_tag'] ) )
        {
            $byline = '<'.$atts['html_tag'] .' id="'.$atts['html_id'].'" class="'.$atts['html_class'].'">'. $byline . '</'.$atts['html_tag'].'>';
        }
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/post_byline', '__return_true' );
        return apply_filters( 'authorship_pro/post_byline', $byline );
    }
}

/*
add_filter( 'authorship/author/link', function( $link, $name, $url, $author_id, $author_type, $author )
{
    if ( is_single() and apply_filters( '_authorship/doing_shortcode/post_byline', false ) )
    {
        $options = authorship_get_options();
        if ( ( $options['author_box'] ) )
        {
            if ( !empty( $name ) )
            {
                $url   = '#';
                $click = "(function(){ window.scrollTo(0, document.querySelector('.m-a-box').offsetTop); return false; })();return false;";
                $link  = '<a href="'.$url.'" onclick="'.$click.'">'.$name.'</a>';
            }
        }
    }

    return $link;
}, 10, 6 );
*/