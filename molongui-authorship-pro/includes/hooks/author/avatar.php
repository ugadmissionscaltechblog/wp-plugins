<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_add_image_sizes' ) )
{
    function authorship_pro_add_image_sizes()
    {
        add_image_size(
            'authorship-post-flat',
            apply_filters( 'authorship_pro/sc/posts_flat_width' , 70 ),
            apply_filters( 'authorship_pro/sc/posts_flat_height', 70 ),
            true
        );
        add_image_size(
            'authorship-post-cards',
            apply_filters( 'authorship_pro/sc/posts_card_width' , 280 ),
            apply_filters( 'authorship_pro/sc/posts_card_height', 189 ),
            true
        );
        add_image_size(
            'authorship-post-thumbs',
            apply_filters( 'authorship_pro/sc/posts_thumb_width' , 180 ),
            apply_filters( 'authorship_pro/sc/posts_thumb_height', 100 ),
            true
        );
    }
    add_action( 'authorship/add_image_size', 'authorship_pro_add_image_sizes' );
}