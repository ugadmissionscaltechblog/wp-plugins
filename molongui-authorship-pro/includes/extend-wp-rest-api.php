<?php

namespace Molongui\Authorship\Pro\Includes;

use Molongui\Authorship\Includes\Author;
\defined( 'ABSPATH' ) or exit;
if ( !\molongui_is_request( 'api' ) ) return;

if ( !\class_exists( 'Extend_WP_REST_API' ) )
{
    class Extend_WP_REST_API
    {
        private $cpts = array();
        function __construct()
        {
            if ( \authorship_is_feature_enabled( 'guest' ) and \authorship_is_feature_enabled( 'guest_in_api' ) )
            {
                $this->expose_guests();
            }
            if ( \authorship_is_feature_enabled( 'author_in_api' ) )
            {
                $this->cpts = \molongui_supported_post_types( MOLONGUI_AUTHORSHIP_NAME, 'all' );
                \add_action( 'rest_api_init', array( $this, 'add_post_authors' ) );
                foreach( $this->cpts as $cpt )
                {
                    \add_filter( 'rest_prepare_'.$cpt, array( $this, 'add_author_links' ), 10, 3 );
                }
            }
        }
        public function expose_guests()
        {
            \add_action( 'rest_api_init', 'authorship_guest_register_post_type' );
            \add_action( 'rest_api_init', array( $this, 'expose_custom_fields' ) );
            \add_filter( 'register_post_type_args', array( $this, 'show_guests' ), 10, 2 );
        }
        public function expose_custom_fields()
        {
            \register_rest_field( MOLONGUI_AUTHORSHIP_CPT, 'author_data', array
            (
                'get_callback' => array( $this, 'get_guest_meta_for_api' ),
                'schema'       => null,
            ));
        }
        public function get_guest_meta_for_api( $object )
        {
            $guest_id = $object['id'];
            $guest = get_post( $guest_id );
            return array
            (
                'display_name' => \get_post_meta( $guest_id, '_molongui_guest_author_display_name', true ),
                'first_name'   => \get_post_meta( $guest_id, '_molongui_guest_author_first_name'  , true ),
                'last_name'    => \get_post_meta( $guest_id, '_molongui_guest_author_last_name'   , true ),
                'full_bio'     => $guest->post_content,
                'short_bio'    => \get_post_meta( $guest_id, '_molongui_guest_author_short_bio'   , true ),
                'avatar'       => \get_post_meta( $guest_id, '_molongui_guest_author_image_url'   , true ),
                'email'        => \get_post_meta( $guest_id, '_molongui_guest_author_mail'        , true ),
                'website'      => \get_post_meta( $guest_id, '_molongui_guest_author_web'         , true ),
                'phone'        => \get_post_meta( $guest_id, '_molongui_guest_author_phone'       , true ),
                'job'          => \get_post_meta( $guest_id, '_molongui_guest_author_job'         , true ),
                'company'      => \get_post_meta( $guest_id, '_molongui_guest_author_company'     , true ),
                'company_url'  => \get_post_meta( $guest_id, '_molongui_guest_author_company_link', true ),
                'post_count'   => \get_post_meta( $guest_id, '_molongui_guest_author_post_count'  , true ),
                'page_count'   => \get_post_meta( $guest_id, '_molongui_guest_author_page_count'  , true ),
                'box_display'  => \get_post_meta( $guest_id, '_molongui_guest_author_box_display' , true ),
            );
        }
        public function show_guests( $args, $post_type )
        {
            if ( 'guest_author' === $post_type )
            {
                $args['show_in_rest']          = true;
                $args['rest_base']             = 'guests';
                $args['rest_controller_class'] = 'WP_REST_Posts_Controller';
            }
            return $args;
        }
        public function add_post_authors()
        {
            foreach( $this->cpts as $cpt )
            {
                \register_rest_field( $cpt, 'authors', array
                (
                    'get_callback' => function( $post )
                    {
                        $authors = \get_post_authors( $post['id'] );
                        foreach ( $authors as $author )
                        {
                            unset( $author->ref );
                            $author_class = new Author( $author->id, $author->type );
                            $author->url  = $author_class->get_url();
                        }
                        return $authors;
                    },
                    'update_callback' => function( $value, $object, $fieldName )
                    {
                        if ( $fieldName !== 'authors' ) return;
                        $value = \maybe_unserialize( $value );
                        if ( !\is_array( $value ) ) $value = array( $value );
                        \delete_post_meta( $object->ID, '_molongui_author' );
                        \update_post_meta( $object->ID, '_molongui_main_author', $value[0]['type'].'-'.$value[0]['id'] );
                        $new_post_author = false;
                        foreach ( $value as $author )
                        {
                            \add_post_meta( $object->ID, '_molongui_author', $author['type'].'-'.$author['id'], false );
                            if ( !$new_post_author and 'user' == $author['type'] ) $new_post_author = $author['id'];
                        }
                        if ( $new_post_author )
                        {
                            $ret = \wp_update_post( array
                            (
                                'ID'          => $object->ID,
                                'post_author' => (int) $new_post_author,
                            ));

                            if ( false === $ret )
                            {
                                return new \WP_Error(
                                    'rest_post_authors_failed',
                                    __( 'Failed to update post_author field.' ),
                                    array( 'status' => 500 )
                                );
                            }
                        }

                        return true;
                    },

                    'schema' => array
                    (
                        'description' => __( 'Molongui authors', 'molongui-authorship-pro' ),
                        'type'        => 'object'
                    ),
                ));
            }
        }
        public function add_author_links( $result, $post, $request )
        {
            if ( $authors = \get_post_authors( $post->ID ) )
            {
                $links = array();
                $links['authors'] = array();
                foreach ( $authors as $key => $author )
                {
                    $author_class = new Author( $author->id, $author->type );
                    $links['authors'][$key]['href']    = \get_rest_url( null, '/wp/v2/' . ( $author->type == 'user' ? 'users' : 'guests' ) . '/' . $author->id );
                    $links['authors'][$key]['archive'] = $author_class->get_url();
                }
                $result->add_links( $links );
            }

            return $result;
        }
    } // class
} // if_class
if ( \class_exists( 'Molongui\Authorship\Pro\Includes\Extend_WP_REST_API' ) )
{
    new Extend_WP_REST_API();
}