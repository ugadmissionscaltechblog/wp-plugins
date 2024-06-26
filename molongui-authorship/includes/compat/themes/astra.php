<?php
defined( 'ABSPATH' ) or exit;
authorship_add_byline_support();
add_filter( 'molongui_authorship_do_filter_name', function( $leave, &$args )
{
    if ( $leave ) return $leave;
    if ( isset( $args['dbt'][3]['function'] ) and ( $args['dbt'][3]['function'] == 'get_the_author' ) and isset( $args['dbt'][4]['function'] ) and ( $args['dbt'][4]['function'] == 'astra_archive_page_info' ) )
    {
        $args['display_name'] = authorship_filter_archive_title( $args['display_name'] );
        return true;
    }
    return false;
}, 10, 2 );
add_filter( 'get_the_author_user_email', function( $value, $user_id = null, $original_user_id = null )
{
    $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 5 );
    $i = 3;
    if ( isset( $dbt[$i]['function'] ) and ( $dbt[$i]['function'] == 'get_the_author_meta' ) and isset( $dbt[$i+1]['function'] ) and ( $dbt[$i+1]['function'] == 'astra_archive_page_info' ) )
    {
        global $wp_query;
        $author_id = ( is_guest_author() and isset( $wp_query->guest_author_id ) ) ? $wp_query->guest_author_id : $wp_query->query_vars['author'];
        $author_class = new Molongui\Authorship\Includes\Author( $author_id, !empty( $wp_query->is_guest_author ) ? 'guest' : 'user' );
        return $author_class->get_mail();
    }
    return $value;
}, 10, 3 );
add_filter( 'get_the_author_description', function( $value, $user_id, $original_user_id )
{
    if ( is_author() )
    {
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 15 );
        $fn  = 'astra_archive_page_info';

        if ( $key = array_search( $fn, array_column( $dbt, 'function' ) ) )
        {
            $value = wpautop( $value ); // Doesn't add any extra spacing between new lines. Check CSS
        }
    }

    return $value;

}, 10, 3 );