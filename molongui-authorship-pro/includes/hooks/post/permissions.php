<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
function authorship_pro_hide_others_posts( $wp_query )
{
    $current_user = wp_get_current_user();
    $roles        = (array) $current_user->roles;
    $options      = Settings::get();
    if ( !empty( $options['permissions_hide_others_posts'] ) )
    {
        if ( array_intersect( $roles, explode( ",", $options['permissions_hide_others_posts'] ) ) )
        {
            if ( !authorship_is_post_type_enabled() ) return;

            global $pagenow;
            if ( !is_admin() or !$wp_query->is_main_query() or 'edit.php' != $pagenow ) return;

            authorship_add_author_meta_query( $wp_query, 'user', $current_user->ID );
            add_filter( 'views_edit-post', 'authorship_pro_remove_view_filters' );
            add_filter( 'views_edit-page', 'authorship_pro_remove_view_filters' );
            add_action( 'admin_print_footer_scripts-edit.php', 'authorship_pro_fix_views_count' );
        }
    }
}
add_action( 'pre_get_posts', 'authorship_pro_hide_others_posts', PHP_INT_MAX );
function authorship_pro_remove_view_filters( $views )
{
    unset( $views['all'] );
    unset( $views['publish'] );

    return $views;
}
function authorship_pro_fix_views_count()
{
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($)
        {
            $('.subsubsub .draft .count').html("");
            $('.subsubsub .trash .count').html("");
        });
    </script>
    <?php
}