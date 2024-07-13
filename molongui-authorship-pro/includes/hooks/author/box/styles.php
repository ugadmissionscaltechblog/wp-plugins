<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_box_styles( $default )
{
    return apply_filters( 'authorship_pro/box/styles', MOLONGUI_AUTHORSHIP_PRO_FOLDER . ( is_rtl() ? '/assets/css/author-box-rtl.71d9.min.css' : '/assets/css/author-box.f886.min.css' ), $default );
}
add_filter( 'authorship/box/styles', 'authorship_pro_box_styles' );
function authorship_pro_box_extra_styles( $css = '', $options = array() )
{
    $css_pro = '';
    $css_pro = apply_filters( 'authorship_pro/box/extra_styles', $css_pro );
    return $css . $css_pro;
}
add_filter( 'authorship/box/extra_styles', 'authorship_pro_box_extra_styles', 10, 2 );
function authorship_pro_eqcss_fallback( $eqcss, $bp, $item_spacing )
{
    $eqcss .= '.m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-2].m-a-box-profile .m-a-box-content-middle { flex-direction: row-reverse; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-2].m-a-box-profile .m-a-box-avatar { padding: 0; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-2].m-a-box-profile .m-a-box-data { padding: 0 '.$item_spacing.' 0 0; }
    ';
    $eqcss .= '.m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-3].m-a-box-profile .m-a-box-avatar { order: 2; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-3].m-a-box-profile .m-a-box-social { order: 1; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-3].m-a-box-profile .m-a-box-data   { order: 3; }
    ';
    $eqcss .= '.m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-4].m-a-box-profile .m-a-box-avatar { order: 2; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-4].m-a-box-profile .m-a-box-social { order: 3; padding: 0; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-4].m-a-box-profile .m-a-box-data   { order: 1; padding: 0 '.$item_spacing.' 0 0; }
    ';
    $eqcss .= '.m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-5].m-a-box-profile .m-a-box-avatar { order: 1; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-5].m-a-box-profile .m-a-box-social { order: 3; padding: 0; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-5].m-a-box-profile .m-a-box-data   { order: 2; padding: 0 '.$item_spacing.' 0 0; }
    ';
    $eqcss .= '.m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-6].m-a-box-profile .m-a-box-avatar { order: 3; padding: 0; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-6].m-a-box-profile .m-a-box-social { order: 1; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-6].m-a-box-profile .m-a-box-data   { order: 2; padding: 0 '.$item_spacing.' 0 0; }
    ';
    $eqcss .= '.m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-7].m-a-box-profile .m-a-box-content-middle { flex-direction: row; flex-wrap: wrap; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-7].m-a-box-profile .m-a-box-data { flex: 1 0; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-7].m-a-box-profile .m-a-box-social { flex-direction: row; align-self: start; margin: 10px 0; padding: 0 '.$item_spacing.'; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-8].m-a-box-profile .m-a-box-content-middle { flex-direction: row; flex-wrap: wrap; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-8].m-a-box-profile .m-a-box-data { flex: 1 0; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-8].m-a-box-profile .m-a-box-social { flex-direction: row; align-self: start; margin: 10px 0; padding: 0 '.$item_spacing.'; }
    ';
    $eqcss .= '.m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-8].m-a-box-profile .m-a-box-content-middle { flex-direction: row-reverse; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-8].m-a-box-profile .m-a-box-avatar { padding: 0; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-8].m-a-box-profile .m-a-box-data { padding: 0 '.$item_spacing.' 0 0; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-8].m-a-box-profile .m-a-box-social { align-self: end; }
               .m-a-box-container[min-width~="'.$bp.'"] .m-a-box-content[data-profile-layout=layout-8].m-a-box-profile .m-a-box-social .m-a-box-social-icon:last-child { margin-right: 0; }
    ';
    return $eqcss;
}
add_filter( 'authorship/eqcss/fallback', 'authorship_pro_eqcss_fallback', 10, 3 );
function authorship_pro_box_extra_css( $css, $options, $box_id = '' )
{
    if ( in_array( $options['author_box_profile_layout'], array( 'layout-7', 'layout-8' ) ) )
    {
        $styles  = '';
        $styles .= !empty( $options['author_box_bottom_background_color'] ) ? 'background-color:'.$options['author_box_bottom_background_color'].';' : ''; // '' = inherit
        $styles .= !empty( $options['author_box_bottom_border_style'] ) ? 'border-top-style:'.$options['author_box_bottom_border_style'].';' : '';
        $styles .= !empty( $options['author_box_bottom_border_width'] ) ? 'border-top-width:'.$options['author_box_bottom_border_width'].'px;' : '';
        $styles .= !empty( $options['author_box_bottom_border_color'] ) ? 'border-top-color:'.$options['author_box_bottom_border_color'].';' : ''; // '' = inherit
        if ( !empty( $styles ) ) $css .= $box_id.' .m-a-box-profile .m-a-box-content-bottom {' . $styles . '}';
    }
    if ( in_array( $options['author_box_related_layout'], array( 'layout-2', 'layout-3' ) ) )
    {
        $styles  = '';
        $styles .= !empty( $options['author_box_related_text_align'] ) ? 'text-align:'.$options['author_box_related_text_align'].';' : '';
        if ( !empty( $styles ) ) $css .= $box_id.' .m-a-box-related-entry-date {' . $styles . '}';
    }

    return $css;
}
add_action( 'authorship/get_box_styles', 'authorship_pro_box_extra_css', 10, 3 );