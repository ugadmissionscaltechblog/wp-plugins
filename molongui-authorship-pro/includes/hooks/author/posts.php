<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_posts_extra_styles( $css = '', $options = array() )
{
    $extra = '';
    $extra .= '.m-a-posts-list[data-list-layout="cards"] { --m-post-card-w: ' . apply_filters( 'authorship_pro/sc/posts_card_width' , 280 ) . 'px; --m-post-card-h: ' . apply_filters( 'authorship_pro/sc/posts_card_height', 189 ) . 'px; }';
    $extra .= '.m-a-posts-list[data-list-layout="flat"] { --m-post-flat-w: ' . apply_filters( 'authorship_pro/sc/posts_flat_width' , 70 ) . 'px; --m-post-flat-h: ' . apply_filters( 'authorship_pro/sc/posts_flat_height', 70 ) . 'px; }';
    $extra .= '.m-a-posts-list[data-list-layout="thumbs"] { --m-post-thumb-w: ' . apply_filters( 'authorship_pro/sc/posts_thumb_width' , 180 ) . 'px; --m-post-thumb-h: ' . apply_filters( 'authorship_pro/sc/posts_thumb_height', 100 ) . 'px; }';
    $extra = apply_filters( 'authorship_pro/posts/extra_styles', $extra );
    return $css . $extra;
}
add_filter( 'authorship/posts/extra_styles', 'authorship_pro_posts_extra_styles', 10, 2 );