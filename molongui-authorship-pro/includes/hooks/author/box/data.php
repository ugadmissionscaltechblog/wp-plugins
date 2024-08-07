<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_maybe_replace_author_name' ) )
{
    function authorship_pro_maybe_replace_author_name( $string, $author )
    {
        if ( !is_array( $author ) ) return $string;
        $string = stripslashes( $string );
        $string = str_replace( '{author_name}', $author['name'], $string );
        return $string;
    }

    add_filter( 'authorship/box/header'       , 'authorship_pro_maybe_replace_author_name', 10, 2 );
    add_filter( 'authorship/box/meta/more'    , 'authorship_pro_maybe_replace_author_name', 10, 2 );
    add_filter( 'authorship/box/meta/bio'     , 'authorship_pro_maybe_replace_author_name', 10, 2 );
    add_filter( 'authorship/box/profile/title', 'authorship_pro_maybe_replace_author_name', 10, 2 );
    add_filter( 'authorship/box/related/title', 'authorship_pro_maybe_replace_author_name', 10, 2 );
}
if ( !function_exists( 'authorship_pro_maybe_replace_author_web' ) )
{
    function authorship_pro_maybe_replace_author_web( $string, $author )
    {
        $string = authorship_pro_maybe_replace_author_name( $string, $author );
        $key = '{raw_link}';
        if ( strpos( $string, $key ) !== false ) $string = esc_url( str_replace( $key, $author['web'], $string ) );
        return $string;
    }
    add_filter( 'authorship/box/meta/web', 'authorship_pro_maybe_replace_author_web', 10, 2 );
}
if ( !function_exists( 'authorship_pro_encode_email' ) )
{
    function authorship_pro_encode_email( $html, $email, $add_microdata, $nofollow )
    {
        $options = Settings::get();

        if ( !empty( $options['encode_email'] ) )
        {
            $email = molongui_ascii_encode( $email );
            $html  = '<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;'.$email.'" target="_top"'. ( $add_microdata ? ' itemprop="email"' : '' ) . ' content="'.$email.'" '.$nofollow.'>' . $email . '</a>';
        }

        return $html;
    }
    add_filter( 'authorship/box/meta/email', 'authorship_pro_encode_email', 10, 4 );
}
if ( !function_exists( 'authorship_pro_encode_phone' ) )
{
    function authorship_pro_encode_phone( $html, $phone, $add_microdata, $nofollow )
    {
        $options = Settings::get();

        if ( !empty( $options['encode_phone'] ) )
        {
            $phone = \molongui_ascii_encode( $phone );
            $html  = '<a href="&#116;&#101;&#108;&#58;'.$phone.'"'. ( $add_microdata ? ' itemprop="telephone"' : '' ) . ' content="'.$phone.'" '.$nofollow.'>' . $phone . '</a>';
        }

        return $html;
    }
    add_filter( 'authorship/box/meta/phone', 'authorship_pro_encode_phone', 10, 4 );
}
function authorship_pro_enable_short_bio( $bio, $author )
{
    $short_bio = '';
    $options   = Settings::get();

    if ( 'short' === $options['author_box_bio_source'] or apply_filters( 'authorship_pro/force_short_bio', false ) )
    {
        $short_bio = authorship_pro_get_author_short_bio( $author['id'], $author['type'], $author );
    }
    return !empty( $short_bio ) ? $short_bio : $bio;
}
add_filter( 'authorship/box/bio', 'authorship_pro_enable_short_bio', 11, 2 );
function authorship_pro_enable_bio_shortcodes( $bio )
{
    $bio = $GLOBALS['wp_embed']->autoembed( $bio );
    return do_shortcode( $bio );
}
add_filter( 'authorship/box/bio', 'authorship_pro_enable_bio_shortcodes', 99, 1 );