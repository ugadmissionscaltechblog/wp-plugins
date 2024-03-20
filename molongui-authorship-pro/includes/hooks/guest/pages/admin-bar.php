<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_admin_bar_edit_user_item( $wp_admin_bar )
{
    if ( !is_admin() )
    {
        if ( is_guest_author() )
        {
            $wp_admin_bar->remove_node( 'edit' );
            global $wp_the_query, $wp_query;
            if ( isset( $wp_query->guest_author_id ) )
            {
                $guest_id = $wp_query->guest_author_id; //$wp_the_query->get_queried_object();
                $wp_admin_bar->add_node
                (
                    array
                    (
                        'id'    => 'edit',
                        'title' => __( "Edit Guest", 'molongui-authorship-pro' ),
                        'href'  => get_edit_post_link( $guest_id ),
                    )
                );
            }
        }
    }
}
add_action( 'admin_bar_menu', 'authorship_pro_admin_bar_edit_user_item', 81 );