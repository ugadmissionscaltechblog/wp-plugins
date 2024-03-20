<?php
defined( 'ABSPATH' ) or exit;
add_filter( '_authorship/filter/get_user_by', function( $data, $args )
{
    list( $filter, $user ) = $data;
    if ( array_intersect( array( 'pmpromd_profile_shortcode', 'pmpromd_shortcode' ), array_column( $args['dbt'], 'function' ) ) ) $filter = false;
    return array( $filter, $user );
}, 10, 2 );