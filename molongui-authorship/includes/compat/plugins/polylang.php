<?php
defined( 'ABSPATH' ) or exit;
add_filter( 'authorship/author/id', function( $id, $type )
{
    if ( empty( $id ) or empty( $type ) ) return $id;

    if ( 'guest' === $type and function_exists ( 'pll_get_post' ) )
    {
        $translated_id = pll_get_post( $id );

        if ( !empty( $translated_id ) and is_int( $translated_id ) and $translated_id != $id )
        {
            $id = $translated_id;
        }
        else
        {
        }
    }

    return $id;

}, 10, 2 );
function authorship_pll_do_not_filter( $query )
{
    if ( !molongui_is_request( 'ajax' ) ) return;

    $dbt   = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 );
    $fn    = 'get_posts_count';
    $class = 'Molongui\Authorship\Includes\Author';
   if ( ( $key = array_search( $fn, array_column( $dbt, 'function' ) ) and
          isset( $dbt[$key]['class'] ) and ( $dbt[$key]['class'] == $class ) )
          or
          !apply_filters( 'authorship/pll_filter_query', true ) )
    {
       $query->set( 'lang', '' );
   }
}
//add_action( 'parse_query', 'authorship_pll_do_not_filter', 0 );