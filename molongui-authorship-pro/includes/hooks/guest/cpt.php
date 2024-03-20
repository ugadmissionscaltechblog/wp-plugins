<?php
defined( 'ABSPATH' ) or exit;
if ( !function_exists( 'authorship_pro_filter_guest_post_type_args' ) )
{
    function authorship_pro_filter_guest_post_type_args( $args, $post_type )
    {
        if ( 'guest_author' === $post_type )
        {
            $options = authorship_get_options();

            $args['exclude_from_search'] = apply_filters( 'authorship_pro/guest/search', empty( $options['enable_guests_in_search'] ) );
            $args['publicly_queryable']  = apply_filters( 'authorship_pro/guest/queryable', true );
            $args['can_export']          = true;
        }
        return $args;
    }
    add_filter( 'register_post_type_args', 'authorship_pro_filter_guest_post_type_args', 10, 2 );
}