<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_filter_author_bio( $bio, $author_id, $author_type, $author )
{
    if ( apply_filters( '_authorship/doing_shortcode/author_bio', false ) ) return $bio;
    $options = apply_filters( '_authorship/get_options', authorship_get_options() );
    if ( 'short' !== $options['author_box_bio_source'] or \is_author() or \is_guest_author() ) return $bio;
    $short_bio = authorship_pro_get_author_short_bio( $author_id, $author_type, $author );
    $short_bio = apply_filters( 'authorship_pro/author/short_bio', $short_bio, $bio, $author_id, $author_type, $author );
    return $short_bio ? $short_bio : $bio;
}
//add_filter( 'authorship/author/bio', 'authorship_pro_filter_author_bio', 10, 4 );