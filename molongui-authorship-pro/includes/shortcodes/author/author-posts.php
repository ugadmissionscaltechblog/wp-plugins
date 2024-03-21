<?php

use Molongui\Authorship\Includes\Author;
defined( 'ABSPATH' ) or exit;

add_shortcode( 'molongui_author_posts', 'shortcode_molongui_author_posts' );
if ( !function_exists( 'shortcode_molongui_author_posts' ) )
{
    function shortcode_molongui_author_posts( $attributes )
    {
        if ( !empty( $attributes['author'] ) and !is_numeric( $attributes['author'] ) )
        {
            $alert  = '<code class="m-a-warning">';
            $alert .= sprintf( __( 'Doing it wrong. Optional "author" attribute must be an integer (the author ID). %sLearn more%s', 'molongui-authorship-pro' ), '<a href="'.MOLONGUI_AUTHORSHIP_FW_URL_DOCS.'/molongui-authorship/shortcodes/molongui_author_posts/" target="_blank">', '</a>' );
            $alert .= '</code>';
            return $alert;
        }
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_posts', '__return_true' );
		if ( isset( $attributes['max_posts'] )  ) $attributes['post_count'] = $attributes['max_posts'];
		if ( isset( $attributes['post_count'] ) ) $attributes['count']      = $attributes['post_count'];
		if ( isset( $attributes['list_class'] ) ) $attributes['class']      = $attributes['list_class'];
        $atts = shortcode_atts( array
        (
            'author'          => '',
            'guest'           => 'no',
            'post_types'      => 'selected',
            'category'        => 0,
            'include_posts'   => array(),
            'exclude_posts'   => array(),
            'count'           => false,
            'paginate'        => false,
            'order'           => 'DESC',
            'orderby'         => 'date',
            'layout'          => 'flat',
            'class'           => '',
            'list_icon'       => 'feather',
            'list_divider'    => 'false', // Must be a string, cannot be a bool.
            'link_color'      => '',
            'link_decoration' => '',
            'show_byline'     => 'true',  // Must be a string, cannot be a bool.
            'show_date'       => 'true',  // Must be a string, cannot be a bool.
            'show_excerpt'    => 'true',  // Must be a string, cannot be a bool.
            'excerpt_length'  => 25,
            'meta_key'        => '',
            'meta_value'      => '',
        ), array_map( 'strtolower', (array)$attributes ) );
        $atts['guest']        = in_array( strtolower( $atts['guest'] ), array( 'yes', 'true' ) ) ? 'guest' : 'user';
        $atts['layout']       = in_array( strtolower( $atts['layout'] ), array( 'plain', 'flat', 'thumbs', 'cards', 'preview' ) ) ? $atts['layout'] : 'flat';// Parse array attributes (remove whitespaces if any and explode string) if needed.
        $atts['count']        = $atts['count'] ? ( is_numeric( $atts['count'] ) ? $atts['count'] : false ) : false;
        $atts['paginate']     = ( $atts['paginate'] and is_numeric( $atts['paginate'] ) ) ? $atts['paginate'] : false;
        $atts['list_divider'] = in_array( strtolower( $atts['list_divider'] ), array( 'yes', 'true' ) ) ? true : false;
        $atts['show_byline']  = in_array( strtolower( $atts['show_byline'] ), array( 'yes', 'true' ) ) ? true : false;
        $atts['show_date']    = in_array( strtolower( $atts['show_date'] ), array( 'yes', 'true' ) ) ? true : false;
        $atts['show_excerpt'] = in_array( strtolower( $atts['show_excerpt'] ), array( 'yes', 'true' ) ) ? true : false;
        $meta_query           = ( !empty( $atts['meta_key'] ) and !empty( $atts['meta_value'] ) ) ? array( 'key' => $atts['meta_key'], 'value' => $atts['meta_value'] ) : array();
        if ( empty( $atts['author'] ) )
        {
            $queried_object = get_queried_object();
            if ( $queried_object instanceof WP_User )
            {
                if ( isset( $queried_object->data->guest_id ) )
                {
                    $atts['author'] = $queried_object->data->guest_id;
                    $atts['guest']  = 'guest';
                }
                else
                {
                    $atts['author'] = $queried_object->ID;
                    $atts['guest']  = 'user';
                }
            }
            else
            {
                wp_reset_query();
                global $post;

                $main_author    = get_main_author( $post->ID );
                $atts['author'] = $main_author->id;
                $atts['guest']  = $main_author->type;
            }
        }
        $atts['category']      = is_array( $atts['category'] )      ? $atts['category']      : molongui_parse_array_attribute( $atts['category'] );
        $atts['include_posts'] = is_array( $atts['include_posts'] ) ? $atts['include_posts'] : molongui_parse_array_attribute( $atts['include_posts'] );
        $atts['exclude_posts'] = is_array( $atts['exclude_posts'] ) ? $atts['exclude_posts'] : molongui_parse_array_attribute( $atts['exclude_posts'] );
        $atts = apply_filters( 'authorship_pro/sc/author_posts/atts', $atts, $attributes );
        authorship_debug( $attributes, "[molongui_author_posts] provided attributes:" );
        authorship_debug( $atts, "[molongui_author_posts] sanitized attributes:" );
        $author = new Author( $atts['author'], $atts['guest'] );
        $posts  = $author->get_posts( array( 'post_type' => $atts['post_types'], 'post_status' => 'publish', 'cat' => $atts['category'], 'post__not_in' => $atts['exclude_posts'], 'post__in' => $atts['include_posts'], 'order' => $atts['order'], 'orderby' => $atts['orderby'], 'posts_per_page' => $atts['count'], 'meta_query' => $meta_query ) );
        $add_microdata = authorship_is_feature_enabled( 'microdata' );
        $styles = apply_filters( 'authorship_pro/posts/styles', MOLONGUI_AUTHORSHIP_PRO_FOLDER . ( is_rtl() ? '/assets/css/author-posts-rtl.c2fa.min.css' : '/assets/css/author-posts.fe46.min.css' ) );
        authorship_pro_register_style( $styles, 'posts' );
        authorship_pro_enqueue_style( $styles, 'posts' );
        ob_start();

        if ( !empty( $posts ) )
        {
            if ( $atts['paginate'] )
            {
                $url_p  = 'm_post_list';                              // Custom URL parameter label.
                $total  = count( $posts );                            // Total items in array.
                $limit  = $atts['paginate'];                          // Authors per page.
                $page   = isset( $_GET[$url_p] ) ? $_GET[$url_p] : 0; // Current page.
                $pages  = ceil( $total/$limit );                // Calculate total pages.
                $page   = max( $page, 1 );                     // Get '1' when $page <= 0
                $page   = min( $page, $pages );                       // Get last page when $page > $pages
                $offset = ( $page - 1 ) * $limit;

                if ( $offset < 0 ) $offset = 0;
                $posts = array_slice( $posts, $offset, $limit );
            }
            $template = apply_filters( 'authorship_pro/author_posts/template', MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/posts-list/html-'.$atts['layout'].'.php' );
            if ( file_exists( $template ) )
            {
                include $template;
            }
            if ( $atts['paginate'] )
            {
                echo '<nav class="m-a-post-pagination pagination" role="navigation">';
                echo paginate_links( array
                (
                    'format'    => '?'.$url_p.'=%#%', //'%#%', // Use a custom URL parameter.
                    'current'   => max( 1, $page ),
                    'total'     => $pages,
                    'prev_text' => __( '&larr;' ),
                    'next_text' => __( '&rarr;' ),
                ) );
                echo '</nav>';
            }
        }
        else
        {
            $no_posts = '<p>' . __( 'There are no posts to list.', 'molongui-authorship-pro' ) . '</p>';
            echo wp_kses_post( apply_filters( 'authorship_pro/author_posts/no_posts', $no_posts ) );
        }
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_posts', '__return_true' );
        return ( ob_get_clean() );
    }
}