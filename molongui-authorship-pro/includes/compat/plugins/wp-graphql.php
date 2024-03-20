<?php
defined( 'ABSPATH' ) or exit;
add_action( 'graphql_register_types', function()
{
    register_graphql_object_type( 'postAuthor', array
    (
        'description' => __( "Post author", 'molongui-authorship' ),
        'fields'      => array
        (
            'id' => array
            (
                'type'        => 'Int',
                'description' => 'author id'
            ),
            'type' => array
            (
                'type'        => 'String',
                'description' => 'author type'
            ),
            'displayName' => array
            (
                'type'        => 'String',
                'description' => 'display name'
            ),
            'url' => array
            (
                'type'        => 'String',
                'description' => 'url'
            ),
        ),
    ));

    register_graphql_field( 'Post', 'Authors', array
    (
        'description' => __( "Returns the post authors", 'molongui-authorship' ),
        'type'        => array ( 'list_of' => 'postAuthor' ),
        'resolve'     => function( $post )
        {
            $authors      = array();
            $post_authors = get_post_authors( $post->ID );

            foreach ( $post_authors as $post_author )
            {
                $author = new Molongui\Authorship\Includes\Author( $post_author->id, $post_author->type );

                $authors[] = array
                (
                    'id'          => $post_author->id,
                    'type'        => $post_author->type,
                    'displayName' => $author->get_name(),
                    'url'         => $author->get_url(),
                );
            }

            return $authors;
        }
    ));
});
add_filter( 'authorship_pro/guest_author_post_type_args', function( $args )
{
    $args['show_in_graphql']     = true;
    $args['graphql_single_name'] = 'guestAuthor';
    $args['graphql_plural_name'] = 'guestAuthors';

    return $args;
});