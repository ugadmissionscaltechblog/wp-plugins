<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_pro_get_author_short_bio' ) )
{
    function authorship_pro_get_author_short_bio( $author_id, $author_type, $author )
    {
        if ( !empty( $author ) )
        {
            switch ( $author_type )
            {
                case 'user':
                    add_filter( '_authorship/filter/get_user_by', '__return_list_false' );
                    $bio = get_the_author_meta( 'molongui_author_short_bio', $author_id );
                    remove_filter( '_authorship/filter/get_user_by', '__return_list_false' );

                break;

                case 'guest':

                    $bio = get_post_meta( $author_id, '_molongui_guest_author_short_bio', true );

                break;
            }
        }
        return isset( $bio ) ? $bio : false;
    }
}