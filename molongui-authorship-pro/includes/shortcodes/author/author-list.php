<?php

use Molongui\Authorship\Common\Modules\Settings;
use Molongui\Authorship\Common\Utils\Debug;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

add_shortcode( 'molongui_author_list', 'authorship_pro_author_list_shortcode' );
if ( !function_exists( 'authorship_pro_author_list_shortcode' ) )
{
    function authorship_pro_author_list_shortcode( $atts )
    {
        if ( is_admin() )
        {
            if ( !function_exists( 'get_current_screen' ) )
            {
                require_once ABSPATH . '/wp-admin/includes/screen.php';
            }
            $current_screen = get_current_screen();

            if ( ( $current_screen instanceof WP_Screen and method_exists( $current_screen, 'is_block_editor' ) and $current_screen->is_block_editor() )
                 or
                 defined( 'REST_REQUEST' ) and REST_REQUEST )
            {
                return;
            }
        }

        $old_atts = $atts;
        add_filter( '_authorship/doing_shortcode', '__return_true' );
        add_filter( '_authorship/doing_shortcode/author_list', '__return_true' );
        $options = Settings::get();
        $add_microdata = authorship_is_feature_enabled( 'microdata' );
        if ( isset( $atts['orderby'] ) and $atts['orderby'] == 'posts' ) $atts['orderby'] = 'post_count';
        if ( isset( $atts['icon'] ) ) $atts['list_icon'] = $atts['icon'];
        if ( isset( $atts['style'] ) )
        {
            if ( in_array( $atts['style'], array( 'flat', 'basic' ) ) and !isset( $atts['layout'] ) ) $atts['layout'] = $atts['style'];
            elseif ( $atts['style'] == 'complete' and !isset( $atts['output'] ) ) $atts['output'] = 'box';
        }
        if ( isset( $atts['select_id']    ) ) $atts['list_id']    = $atts['select_id'];
        if ( isset( $atts['select_class'] ) ) $atts['list_class'] = $atts['select_class'];
        if ( isset( $atts['select_atts']  ) ) $atts['list_atts']  = $atts['select_atts'];
        if ( isset( $atts['with_posts'] ) and 'yes' === $atts['with_posts'] ) $atts['min_post_count'] = 1;
        if ( isset( $atts['post_types'] ) ) $atts['post_type'] = $atts['post_types'];
        $atts = shortcode_atts( array
        (
            'output'                  => 'list',
            'layout'                  => 'flat',
            'type'                    => 'authors',
            'order'                   => 'ASC',
            'orderby'                 => 'name',
            'user_role'               => array(),
            'include_users'           => array(),
            'exclude_users'           => array(),
            'include_guests'          => array(),
            'exclude_guests'          => array(),
            'exclude_archived'        => 'no',
            'min_post_count'          => 0,
            'post_type'               => array( 'post' ),
            'show_post_count'         => array(),
            'show_post_count_total'   => false,
            'count'                   => false,
            'paginate'                => false,
            'name_format'             => 'display_name',
            'name_link'               => true,
            'avatar_link'             => true,
            'show_bio'                => false,
            'bio_format'              => 'full',
            'list_icon'               => 'feather',
            'list_id'                 => '',
            'list_class'              => '',
            'list_atts'               => '',
            'default_option_label'    => __( "Select an author", 'molongui-authorship-pro' ),
            'default_option_value'    => '',
            'dev_mode'                => 'no', // Private attribute.
            'columns'                 => '',
            'grid-edge-gap'           => '',
            'grid-row-gap'            => '',
            'grid-column-gap'         => '',
            'grid-item-padding'       => '',
            'grid-item-color'         => '',
            'grid-item-border-width'  => '',
            'grid-item-border-style'  => '',
            'grid-item-border-color'  => '',
            'grid-item-border-radius' => '',
        ), array_map( 'strtolower', (array)$atts ) );
        $atts['default_option_label'] = !empty( $old_atts['default_option_label'] ) ? $old_atts['default_option_label'] : __( "Select an author", 'molongui-authorship-pro' );
        $atts['default_option_value'] = !empty( $old_atts['default_option_value'] ) ? $old_atts['default_option_value'] : '';
        $atts['default_option_label'] = apply_filters( 'authorship_pro/author_list/default_select_option_label', $atts['default_option_label'] );
        $atts['default_option_value'] = apply_filters( 'authorship_pro/author_list/default_select_option_value', $atts['default_option_value'] );
        $atts['user_role']       = is_array( $atts['user_role']       ) ? $atts['user_role']       : molongui_parse_array_attribute( $atts['user_role']       );
        $atts['include_users']   = is_array( $atts['include_users']   ) ? $atts['include_users']   : molongui_parse_array_attribute( $atts['include_users']   );
        $atts['exclude_users']   = is_array( $atts['exclude_users']   ) ? $atts['exclude_users']   : molongui_parse_array_attribute( $atts['exclude_users']   );
        $atts['include_guests']  = is_array( $atts['include_guests']  ) ? $atts['include_guests']  : molongui_parse_array_attribute( $atts['include_guests']  );
        $atts['exclude_guests']  = is_array( $atts['exclude_guests']  ) ? $atts['exclude_guests']  : molongui_parse_array_attribute( $atts['exclude_guests']  );
        $atts['post_type']       = is_array( $atts['post_type']       ) ? $atts['post_type']       : molongui_parse_array_attribute( $atts['post_type']       );
        $atts['show_post_count'] = ( 'all' === $atts['show_post_count'] ? $atts['show_post_count'] : ( is_array( $atts['show_post_count'] ) ? $atts['show_post_count'] : molongui_parse_array_attribute( $atts['show_post_count'] ) ) );
        $atts['exclude_archived']      = in_array( strtolower( $atts['exclude_archived'] ), array( 'yes', 'true', 'on' ) ) ? true : false;
        $atts['show_post_count_total'] = in_array( strtolower( $atts['show_post_count_total'] ), array( 'yes', 'true', 'on' ) ) ? true : false;
        $atts['dev_mode']              = ( $atts['dev_mode'] === true or in_array( strtolower( $atts['dev_mode'] ), array( 'yes', 'true', 'on' ) ) ) ? true : false;
        $atts['name_link']             = ( $atts['name_link'] === true or in_array( strtolower( $atts['name_link'] ), array( 'yes', 'true', 'on' ) ) ) ? true : false;
        $atts['avatar_link']           = ( $atts['avatar_link'] === true or in_array( strtolower( $atts['avatar_link'] ), array( 'yes', 'true', 'on' ) ) ) ? true : false;
        $atts['paginate']   = is_numeric( $atts['paginate'] ) ? $atts['paginate'] : false;
        $atts['orderby']    = ( $atts['orderby'] and $atts['orderby'] == 'include' and empty( $atts['include_users'] ) and empty( $atts['include_guests'] ) ) ? 'name' : $atts['orderby'];
        $atts['bio_format'] = in_array( strtolower( $atts['bio_format'] ), array( 'full', 'short' ) ) ? $atts['bio_format'] : 'full';
        $atts['type']       = in_array( strtolower( $atts['type'] ), array( 'user', 'guest', 'author' ) ) ? $atts['type'].'s' : $atts['type'];
        if ( $atts['exclude_archived'] )
        {
            $archived_users  = authorship_get_archived_users();
            $archived_guests = authorship_get_archived_guests();
            $atts['exclude_users']  = array_unique( array_merge( $atts['exclude_users'], $archived_users ) );
            $atts['exclude_guests'] = array_unique( array_merge( $atts['exclude_guests'], $archived_guests ) );
        }
        Debug::console_log( $old_atts, "[molongui_author_list] provided attributes:" );
        Debug::console_log( $atts, "[molongui_author_list] sanitized attributes:" );
        add_filter( 'authorship_pro/author_list/atts', function() use ( $atts ){ return $atts; } );
        add_filter( 'authorship/author/name/format', 'authorship_pro_author_list_name_format' );
        add_filter( 'authorship/user/roles', function() use ( $atts ){ return $atts['user_role']; } );
        if ( 'short' === $atts['bio_format'] )
        {
            add_filter( 'authorship/author/bio', 'authorship_pro_author_list_bio_format', 11, 4 );
        }
        $authors = molongui_get_authors( $atts['type'], $atts['include_users'], $atts['exclude_users'], $atts['include_guests'], $atts['exclude_guests'], $atts['order'], $atts['orderby'], false, $atts['min_post_count'], $atts['post_type'] );
        if ( $atts['count'] and is_numeric( $atts['count'] ) ) $authors = array_slice( $authors, 0, $atts['count'] );
        if ( false === $atts['paginate'] and count( $authors ) > 30 )
        {
            $atts['paginate'] = 10;
        }
        ob_start();

        if ( !empty( $authors ) )
        {
            if ( $atts['paginate'] and $atts['output'] != 'select' )
            {
                $url_p  = 'm_auth_list';                              // Custom URL parameter label.
                $total  = count( $authors );                          // Total items in array.
                $limit  = $atts['paginate'];                          // Authors per page.
                $page   = isset( $_GET[$url_p] ) ? $_GET[$url_p] : 0; // Current page.
                $pages  = ceil( $total/$limit );                // Calculate total pages.
                $page   = max( $page, 1 );                     // Get '1' when $page <= 0
                $page   = min( $page, $pages );                       // Get last page when $page > $pages
                $offset = ( $page - 1 ) * $limit;

                if ( $offset < 0 ) $offset = 0;
                $authors = array_slice( $authors, $offset, $limit );
            }
            if ( !$atts['min_post_count'] )
            {
                $author_type    = 'authors';
                $include_users  = array();
                $include_guests = array();
                foreach ( $authors as $author )
                {
                    if ( 'user' === $author['type'] ) $include_users[] = $author['id'];
                    else $include_guests[] = $author['id'];
                }

                if ( empty( $include_users ) and !empty( $include_guests ) )
                {
                    $author_type = 'guests';
                }
                elseif ( !empty( $include_users ) and empty( $include_guests ) )
                {
                    $author_type = 'users';
                }
                $authors = molongui_get_authors( $author_type, $include_users, array(), $include_guests, array(), $atts['order'], $atts['orderby'], true );
            }
            remove_filter( 'authorship/author/name/format', 'authorship_pro_author_list_name_format' );
            if ( 'short' === $atts['bio_format'] )
            {
                remove_filter( 'authorship/author/bio', 'authorship_pro_author_list_bio_format', 11 );
            }
            $styles = apply_filters( 'authorship_pro/list/styles', MOLONGUI_AUTHORSHIP_PRO_FOLDER . ( is_rtl() ? '/assets/css/author-list-rtl.ce6c.min.css' : '/assets/css/author-list.65d6.min.css' ) );
            if ( 'select' !== $atts['output'] )
            {
                authorship_pro_register_style( $styles, 'list' );
                authorship_pro_enqueue_style( $styles, 'list' );
            }
            switch ( $atts['output'] )
            {
                case 'grid':
                    $template = MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/authors-list/html-'.$atts['output'].'.php';
                break;

                case 'box':
                    $template = MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/authors-list/html-'.$atts['output'].'.php';
                break;

                case 'select':
                    $template = MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/authors-list/html-'.$atts['output'].'.php';
                break;

                case 'list':
                default:
                    $template = MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/authors-list/html-'.$atts['layout'].'.php';
                break;
            }
            $template = apply_filters( 'authorship_pro/authors_list/template', $template );
            if ( file_exists( $template ) )
            {
                 include $template;
            }
            if ( $atts['paginate'] and $atts['output'] != 'select' )
            {
                echo '<nav class="m-a-list-pagination pagination" role="navigation">';
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
            echo '<div><p>'.__( "No authors found.", 'molongui-authorship-pro' ).'</p></div>';
        }

        $output = ob_get_clean();
        remove_filter( '_authorship/doing_shortcode', '__return_true' );
        remove_filter( '_authorship/doing_shortcode/author_list', '__return_true' );
        return $output;
    }
}
if ( !function_exists( 'authorship_pro_author_list_name_format' ) )
{
    function authorship_pro_author_list_name_format()
    {
        $atts = apply_filters( 'authorship_pro/author_list/atts', array() );
        return $atts['name_format'];
    }
}
if ( !function_exists( 'authorship_pro_author_list_bio_format' ) )
{
    function authorship_pro_author_list_bio_format( $bio, $author_id, $author_type, $author )
    {
        $short_bio = \authorship_pro_get_author_short_bio( $author_id, $author_type, $author );
        return $short_bio ? $short_bio : $bio;
    }
}