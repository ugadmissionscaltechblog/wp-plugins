<?php
defined( 'ABSPATH' ) or exit;
add_filter( 'authorship_pro/sc/byline/post_id', function( $post_id )
{
    global $tdb_state_single;
    $tdcl_query = $tdb_state_single->get_wp_query();

    return empty( $tdcl_query->queried_object_id ) ? $post_id : $tdcl_query->queried_object_id;
}, 10, 1 );