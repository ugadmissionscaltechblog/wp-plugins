<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;
add_shortcode( 'author_box'         , 'authorship_author_box_shortcode' );
add_shortcode( 'molongui_author_box', 'authorship_author_box_shortcode' );
if ( !function_exists( 'authorship_author_box_shortcode' ) )
{
    function authorship_author_box_shortcode( $atts )
    {
        if ( !authorship_is_feature_enabled( 'box' ) )
        {
            return '<code class="m-a-warning">' . __( "You need to enable the author box feature in the plugin settings page in order this shortcode to work.", 'molongui-authorship-pro' ) . '</code>';
        }
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_box', '__return_true' );

        if ( !empty( $atts ) and is_array( $atts ) )
        {
            $original_atts = $atts;
            if ( isset( $atts['headline_style'] ) ) $atts['headline_text_style'] = $atts['headline_style'];
            if ( isset( $atts['headline_align'] ) ) $atts['headline_text_align'] = $atts['headline_align'];
            if ( isset( $atts['headline_color'] ) ) $atts['headline_text_color'] = $atts['headline_color'];
            if ( isset( $atts['headline_size']  ) ) $atts['headline_text_size']  = $atts['headline_size'];
            if ( isset( $atts['author'] ) ) $atts['id']   = $atts['author'];
            if ( isset( $atts['guest']  ) ) $atts['type'] = ( strtolower( $atts['guest'] ) == 'yes' ? 'guest' : 'user' );
            if ( function_exists( 'authorship_options_update_20' ) )
            {
                $atts = authorship_options_update_20( $atts );
                if ( !isset( $original_atts['name_link_to_archive'] ) ) unset( $atts['author_box_name_link'] );
                if ( !isset( $original_atts['avatar_link_to_archive'] ) ) unset( $atts['author_box_avatar_link'] );
            }
            $no_prefix = array
            (
                'id',
                'type',
                'force_display',
                'hide_if_no_bio',
                'guest_pages',
                'show_bio',
            );
            foreach ( $atts as $name => $value )
            {
                if ( in_array( $name, $no_prefix ) ) continue;
                $prefix = 'author_box_';
                if ( substr( $name, 0, strlen( $prefix ) ) !== $prefix )
                {
                    $atts[$prefix . $name] = $value;
                    unset( $atts[$name] );
                }
            }
            $bool_atts = array
            (
                'force_display',
                'hide_if_no_bio',
                'guest_pages',
                'show_bio',
                'show_headline',
                'author_box_avatar_show',
                'author_box_meta_show',
                'author_box_social_show',
                'author_box_related_show',
                'author_box_name_underline',
            );
            foreach ( $bool_atts as $bool_att )
            {
                if ( isset( $atts[$bool_att] ) )
                {
                    $atts[$bool_att] = in_array( strtolower( $atts[$bool_att] ), array( 'yes', 'true', 'on', 'display', 'show' ) ) ? true : false;
                }
            }
            $select_atts = array
            (
                'author_box_layout'           => array( 'slim', 'tabbed', 'stacked' ),
                'author_box_profile_layout'   => array( 'layout-1', 'layout-2', 'layout-3', 'layout-4', 'layout-5', 'layout-6', 'layout-7', 'layout-8' ),
                'author_box_related_layout'   => array( 'layout-1', 'layout-2', 'layout-3' ),
                'author_box_social_style'     => array( 'default', 'squared', 'circled', 'boxed', 'branded', 'branded-squared', 'branded-squared-reverse', 'branded-circled', 'branded-circled-reverse', 'branded-boxed' ),
                'author_box_related_order_by' => array( 'date', 'modified', 'title', 'comment_count', 'rand' ),
                'author_box_related_order'    => array( 'asc', 'desc' ),
            );
            foreach ( $select_atts as $key => $accepted )
            {
                if ( isset( $atts[$key] ) )
                {
                    $atts[$key] = in_array( strtolower( $atts[$key] ), $accepted ) ? $atts[$key] : $accepted[0];
                }
            }
            if ( isset( $atts['show_bio'] ) ) $atts['author_box_bio_source'] = $atts['show_bio'] ? $atts['author_box_bio_source'] : 'none';
        }
        $options = shortcode_atts( array_merge( array
        (
            'id'            => '',
            'type'          => 'user',
            'force_display' => true,
            'extra_content' => '',
        ), authorship_get_options() ), array_map( 'strtolower', (array)$atts ) );
        $allowed_tags = array
        (
            'a' => array
            (
                'href'   => array(),
                'target' => array(),
                'title'  => array(),
            ),
            'br'     => array(),
            'em'     => array(),
            'strong' => array(),
        );
        if ( !empty( $options['extra_content'] ) ) $options['extra_content'] = wp_kses( html_entity_decode( $options['extra_content'] ), $allowed_tags );
        if ( empty( $options['id'] ) )
        {
            if ( !$authors = molongui_find_authors() )
            {
                return false;
            }
        }
        else
        {
            $authors          = array();
            $authors[0]       = new stdClass();
            $authors[0]->id   = $options['id'];
            $authors[0]->type = $options['type'];
            $authors[0]->ref  = $options['type'].'-'.$options['id'];
        }
        if ( $options['force_display'] and $options['hide_if_no_bio'] )
        {
            foreach ( $authors as $key => $author )
            {
                $author_class = new Author( $author->id, $author->type );
                if ( !$author_class->get_bio() ) unset( $authors[$key] );
            }
        }
        if ( empty( $authors ) ) return;
        global $post;
        add_filter( 'authorship/skip/query/alias', '__return_true' );
        add_filter( 'authorship/author_box_markup', function ( $markup, $post, $post_authors, $settings, $check, $box_ids ) use ( $options )
        {
            $markup .= '<style id="molongui-author-box-shortcode-inline-css" type="text/css">';
            foreach ( $box_ids as $box_id )
            {
                $markup .= authorship_get_box_styles( $options, $box_id );
            }
            $markup .= '</style>';
            return $markup;
        }, 10, 6 );
        $markup = authorship_box_markup( $post, $authors, $options, !$options['force_display'] );
        remove_filter( 'authorship/skip/query/alias', '__return_true' );
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_box', '__return_true' );
        return $markup;
    }
}