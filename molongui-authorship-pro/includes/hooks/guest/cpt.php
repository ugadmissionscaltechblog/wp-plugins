<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_filter_guest_post_type_args' ) )
{
    function authorship_pro_filter_guest_post_type_args( $args, $post_type )
    {
        if ( 'guest_author' === $post_type )
        {
            $options = Settings::get();

            $args['exclude_from_search'] = apply_filters( 'authorship_pro/guest/search', empty( $options['enable_guests_in_search'] ) );
            $args['publicly_queryable']  = apply_filters( 'authorship_pro/guest/queryable', true );
            $args['can_export']          = true;

            /*!
             * FILTER HOOK
             *
             * Allows filtering the already filtered $args.
             *
             * @since 1.6.4
             */
            $args = apply_filters( 'authorship_pro/guest_author_post_type_args', $args );
        }

        return $args;
    }
    add_filter( 'register_post_type_args', 'authorship_pro_filter_guest_post_type_args', 10, 2 );
}