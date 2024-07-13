<?php

use Molongui\Authorship\Author;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

if ( authorship_is_feature_enabled( 'author_search' ) )
{
    add_filter( 'relevanssi_content_to_index', 'authorship_pro_rlv_add_extra_content_to_index', 10, 2 );
    add_filter( 'relevanssi_excerpt_content' , 'authorship_pro_rlv_add_extra_content_to_index', 10, 2 );
}
if ( !function_exists( 'authorship_pro_rlv_add_extra_content_to_index' ) )
{
    function authorship_pro_rlv_add_extra_content_to_index( $content, $post )
    {
        if ( !in_array( $post->post_type, molongui_supported_post_types( MOLONGUI_AUTHORSHIP_NAME, 'all' ) ) ) return $content;
        $authors = get_post_authors( $post->ID );
        if ( !empty( $authors ) ) foreach ( $authors as $author )
        {
            $author_class = new Author( $author->id, $author->type );
            $author_name  = $author_class->get_name();
            $content     .= ' ' . $author_name;
        }

        return $content;
    }
}