<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
remove_filter( 'authorship/author_box/profile_layout', 'authorship_box_preview_profile_layout', 10 );
remove_filter( 'authorship/author_box/related_layout', 'authorship_box_preview_related_layout', 10 );
function authorship_pro_profile_layout( $output, $options, $author, $random_id )
{
    if ( !empty( $options['author_box_profile_layout'] ) and 'layout-1' !== $options['author_box_profile_layout'] )
    {
        $add_microdata = !empty( $options['box_schema'] );

        ob_start();
        include MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/author-box/profile/html-'.$options['author_box_profile_layout'].'.php';
        $output = ob_get_clean();
    }

    return $output;
}
add_filter( 'authorship/author_box/profile_layout', 'authorship_pro_profile_layout', 10, 4 );
function authorship_pro_related_layout( $output, $options, $author )
{
    $premium_layouts = array( 'layout-3' );
    $add_microdata = !empty( $options['box_schema'] );

    if ( in_array( $options['author_box_related_layout'], $premium_layouts ) )
    {
        ob_start();
        include MOLONGUI_AUTHORSHIP_PRO_DIR . 'views/author-box/related/html-'.$options['author_box_related_layout'].'.php';
        $output = ob_get_clean();
    }

    return $output;
}
add_filter( 'authorship/author_box/related_layout', 'authorship_pro_related_layout', 10, 3 );