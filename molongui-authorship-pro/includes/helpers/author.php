<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_get_author_short_bio( $author_id, $author_type, $author )
{
    $short_bio = '';

    if ( !empty( $author ) )
    {
        switch ( $author_type )
        {
            case 'user':
                add_filter( '_authorship/filter/get_user_by', '__return_list_false' );

                $short_bio = get_the_author_meta( 'molongui_author_short_bio', $author_id );
                remove_filter( '_authorship/filter/get_user_by', '__return_list_false' );

            break;

            case 'guest':

                $short_bio = get_post_meta( $author_id, '_molongui_guest_author_short_bio', true );

            break;
        }
    }
    return apply_filters( 'authorship_pro/author/short_bio', $short_bio, $author_id, $author_type, $author );
}