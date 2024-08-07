<?php

use Molongui\Authorship\Author;
use Molongui\Authorship\Common\Utils\Post;
defined( 'ABSPATH' ) or exit;
function authorship_deprecated_filter_get_author( $author, $author_id, $author_type )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author', array( $author, $author_id, $author_type ), '4.2.0', 'authorship/author/get' );
}
add_filter( 'authorship/author/get', 'authorship_deprecated_filter_get_author', 0, 3 );
function authorship_deprecated_filter_get_name( $value, $author_id, $author_type, $author )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author_name', array( $value, $author_id, $author_type, $author ), '4.2.0', 'authorship/author/name' );
}
add_filter( 'authorship/author/name', 'authorship_deprecated_filter_get_name', 0, 4 );
function authorship_deprecated_filter_get_slug( $value, $author_id, $author_type, $author )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author_slug', array( $value, $author_id, $author_type, $author ), '4.2.0', 'authorship/author/slug' );
}
add_filter( 'authorship/author/slug', 'authorship_deprecated_filter_get_slug', 0, 4 );
function authorship_deprecated_filter_get_url( $value, $author_id, $author_type, $author, $opt )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author_url', array( $value, $author_id, $author_type, $author, $opt ), '4.2.0', 'authorship/author/url' );
}
add_filter( 'authorship/author/url', 'authorship_deprecated_filter_get_url', 0, 5 );
function authorship_deprecated_filter_get_link( $value, $name, $url, $author_id, $author_type, $author )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author_link', array( $value, $name, $url, $author_id, $author_type ), '4.2.0', 'authorship/author/link' );
}
add_filter( 'authorship/author/link', 'authorship_deprecated_filter_get_link', 0, 6 );
function authorship_deprecated_filter_get_bio( $value, $author_id, $author_type, $author )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author_bio', array( $value, $author_id, $author_type, $author ), '4.2.0', 'authorship/author/bio' );
}
add_filter( 'authorship/author/bio', 'authorship_deprecated_filter_get_bio', 0, 4 );
function authorship_deprecated_filter_get_mail( $value, $author_id, $author_type, $author )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author_mail', array( $value, $author_id, $author_type, $author ), '4.2.0', 'authorship/author/mail' );
}
add_filter( 'authorship/author/mail', 'authorship_deprecated_filter_get_mail', 0, 4 );
function authorship_deprecated_filter_get_meta( $value, $author_id, $author_type, $author, $key )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author_meta', array( $value, $author_id, $author_type, $key ), '4.2.0', 'authorship/author/meta' );
}
add_filter( 'authorship/author/meta', 'authorship_deprecated_filter_get_meta', 0, 5 );
function authorship_deprecated_filter_get_post_count( $value, $author_id, $author_type, $author, $post_types )
{
    return apply_filters_deprecated( 'molongui_authorship_get_author_post_count', array( $value, $author_id, $author_type, $author ), '4.2.0', 'authorship/author/post_count' );
}
add_filter( 'authorship/author/post_count', 'authorship_deprecated_filter_get_post_count', 0, 5 );
function authorship_deprecated_filter_link_guest( $value, $guest_id )
{
    $hook_name = 'authorship/filter/link/guest';
    $author    = null;
    $options   = null;
    if ( has_filter( $hook_name ) )
    {
        $author  = new Author( $guest_id, 'guest' );
        $options = authorship_get_options();
    }

    return apply_filters_deprecated( $hook_name, array( $value, $guest_id, 'guest', $author, $options ), '4.2.0', 'authorship/author/url' );
}
add_filter( 'authorship/author/url', 'authorship_deprecated_filter_link_guest', 0, 2 );
function authorship_deprecated_filter_avatar_skip( $value, $args, $dbt )
{
    return apply_filters_deprecated( 'molongui_authorship_dont_filter_avatar', array( $value, $args, $dbt ), '4.2.0', 'authorship/get_avatar_data/skip' );
}
add_filter( 'authorship/get_avatar_data/skip', 'authorship_deprecated_filter_avatar_skip', 0, 3 );
function authorship_deprecated_filter_avatar_post_id( $value )
{
    return apply_filters_deprecated( 'molongui_authorship_filter_avatar_post_id', array( $value ), '4.2.0', '_authorship/get_avatar_data/filter/post_id' );
}
add_filter( '_authorship/get_avatar_data/filter/post_id', 'authorship_deprecated_filter_avatar_post_id', 0, 1 );
function authorship_deprecated_filter_avatar_author( $value, $id_or_email, $dbt )
{
    return apply_filters_deprecated( 'molongui_filter_avatar_author', array( $value, $id_or_email, $dbt ), '4.2.0', '_authorship/get_avatar_data/filter/author' );
}
add_filter( '_authorship/get_avatar_data/filter/author', 'authorship_deprecated_filter_avatar_author', 0, 3 );
function authorship_deprecated_dont_filter_link( $link, $original_link, $author_id, $author_nicename )
{
    $hook_name = 'molongui_authorship_dont_filter_link';
    $dbt = array();
    if ( has_filter( $hook_name ) )
    {
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 );
    }

    $value = $link;
    $args  = array( 'link' => &$original_link, 'author_id' => $author_id, 'author_nicename' => $author_nicename, 'dbt' => $dbt );

    return apply_filters_deprecated( $hook_name, array( $value, $args['link'], $args['dbt'] ), '4.3.9', 'authorship/pre_author_link' );
}
add_filter( 'authorship/pre_author_link', 'authorship_deprecated_dont_filter_link', 0, 4 );
function authorship_deprecated_do_filter_link( $link, $original_link, $author_id, $author_nicename )
{
    $hook_name = 'molongui_authorship_do_filter_link';
    $dbt = array();
    if ( has_filter( $hook_name ) )
    {
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 );
    }

    $value = $link;
    $args  = array( 'link' => &$original_link, 'author_id' => $author_id, 'author_nicename' => $author_nicename, 'dbt' => $dbt );

    return apply_filters_deprecated( $hook_name, array( $value, $args ), '4.3.9', 'authorship/pre_author_link' );
}
add_filter( 'authorship/pre_author_link', 'authorship_deprecated_do_filter_link', 0, 4 );
function authorship_deprecated_filter_author_link( $link, $original_link, $author_id, $author_nicename )
{
    $hook_name = 'authorship/filter_author_link';
    $dbt = array();
    if ( has_filter( $hook_name ) )
    {
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 );
    }

    $value = $link;
    $args  = array( 'link' => &$original_link, 'author_id' => $author_id, 'author_nicename' => $author_nicename, 'dbt' => $dbt );

    return apply_filters_deprecated( $hook_name, array( $value, &$args ), '4.7.0', 'authorship/pre_author_link' );
}
add_filter( 'authorship/pre_author_link', 'authorship_deprecated_filter_author_link', 0, 4 );
function authorship_deprecated_filter_gub_no_loop( $default, $user, $field, $value )
{
    $hook_name = '_authorship/get_user_by/guest/archive/no_loop';
    $dbt       = array();
    if ( has_filter( $hook_name ) )
    {
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 );
    }

    $args = array( 'field' => $field, 'value' => $value, 'dbt' => $dbt );

    return apply_filters_deprecated( $hook_name, array( $default, $user, $args ), '4.6.7', '_authorship/get_user_by/aim' );
}
function authorship_deprecated_filter_gub_guest_loop( $default, $user, $field, $value )
{
    $hook_name = '_authorship/get_user_by/guest/archive/loop';
    $dbt       = array();
    if ( has_filter( $hook_name ) )
    {
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 );
    }

    $byline_name = ( 'byline' === $value ? true : false );
    $args = array( 'field' => $field, 'value' => $value, 'dbt' => $dbt );

    return apply_filters_deprecated( $hook_name, array( $byline_name, $args ), '4.6.7', '_authorship/get_user_by/aim' );
}
add_filter( '_authorship/get_user_by/aim', 'authorship_deprecated_filter_gub_guest_loop', 0, 4 );
function authorship_deprecated_filter_gub_archive_post_id( $post_id, $user, $field, $value )
{
    $hook_name = 'authorship/override/get_user_by/archive/post_id';
    $dbt       = array();
    if ( has_filter( $hook_name ) )
    {
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 );
    }

    return apply_filters_deprecated( $hook_name, array( $post_id, null, $user, $dbt ), '4.6.7', '_authorship/get_user_by/post_id' );
}
function authorship_deprecated_filter_gub_post_id( $post_id, $user, $field, $value )
{
    return apply_filters_deprecated( 'molongui_authorship_override_get_user_by_post_id', array( $post_id, get_post( $post_id ), $user ), '4.6.7', '_authorship/get_user_by/post_id' );
}
add_filter( '_authorship/get_user_by/post_id', 'authorship_deprecated_filter_gub_post_id', 0, 4 );
function authorship_deprecated_filter_get_user_by( $user, $original_user, $field, $value )
{
    $hook_name = '_authorship/filter/get_user_by';
    $wp_query  = null;
    $post      = null;
    $dbt       = array();
    $filter    = true;
    if ( has_filter( $hook_name ) )
    {
        global $wp_query, $post;
        $dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 );
    }

    $args = array( 'field' => $field, 'value' => $value, 'post' => $post, 'query' => $wp_query, 'dbt' => $dbt );

    list( $filter, $user ) = apply_filters_deprecated( $hook_name, array( array( $filter, $original_user ), $args ), '4.7.0', 'authorship/pre_get_user_by' );

    return $filter ? $user : $original_user;
}
function authorship_deprecated_filter_dont_render_box( $value )
{
    $post = Post::get();
    return !apply_filters_deprecated( 'molongui_authorship_dont_render_author_box', array( $value, $post ), '4.4.0', 'authorship/render_box' );
}
function authorship_deprecated_filter_box_hide( $value )
{
    $post = Post::get();
    return !apply_filters_deprecated( 'authorship/box/hide', array( $value, $post ), '4.5.4', 'authorship/render_box' );
}
function authorship_deprecated_filter_post_as_others( $value )
{
    return apply_filters_deprecated( 'authorship/post_as_other_author', array( $value ), '4.8.0', 'authorship/can_post_as_others' );
}
//add_filter( 'authorship/can_post_as_others', 'authorship_deprecated_filter_post_as_others', 0, 1 );