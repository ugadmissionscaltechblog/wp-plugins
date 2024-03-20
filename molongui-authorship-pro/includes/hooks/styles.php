<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_admin_styles( $default )
{
    return apply_filters( 'authorship_pro/admin/styles', MOLONGUI_AUTHORSHIP_PRO_FOLDER . ( is_rtl() ? '/assets/css/admin-rtl.4e67.min.css' : '/assets/css/admin.8d87.min.css' ), $default );
}
add_filter( 'authorship/admin/styles', 'authorship_pro_admin_styles' );
function authorship_pro_admin_extra_styles( $css )
{
    $css_pro  = '';
    $css_pro .= '';
    $css_pro = apply_filters( 'authorship_pro/admin/extra_styles', $css_pro );
    return $css . $css_pro;
}
add_filter( 'authorship/admin/extra_styles', 'authorship_pro_admin_extra_styles', 10, 2 );