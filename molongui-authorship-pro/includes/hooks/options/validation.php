<?php
defined( 'ABSPATH' ) or exit;
remove_filter( 'authorship/validate_options', 'authorship_validate_freemium_options', 10 );
remove_filter( 'authorship/get_options', 'authorship_validate_saved_options', 10 );
function authorship_pro_maybe_flush_rules( $options, $old )
{
    if ( $options['user_archive_base'] !== $old['user_archive_base'] or
         $options['guest_pages'] !== $old['guest_pages'] or
         $options['guest_archive_base'] !== $old['guest_archive_base']
    )
        update_option( MOLONGUI_AUTHORSHIP_PRO_PREFIX.'_flush_rewrite_rules', 1, true );
}
add_action( 'authorship/options', 'authorship_pro_maybe_flush_rules', 10, 2 );
function authorship_pro_maybe_update_post_data( $options, $old )
{
    if ( $options['post_types'] !== $old['post_types'] )
    {
        if ( !empty( $options['post_types'] ) )
        {
            $pt_counters    = array();
            $pt_metafields  = array();
            $old_post_types = explode( ",", $old['post_types'] );

            foreach ( explode( ",", $options['post_types'] ) as $post_type )
            {
                if ( !in_array( $post_type, $old_post_types ) )
                {
                    $pt_metafields[] = $post_type;
                    if ( !in_array( $post_type, array( 'post', 'page' ) ) )
                    {
                        $pt_counters[] = $post_type;
                    }
                }
            }

            if ( !empty( $pt_metafields ) )
            {
                authorship_update_post_authors( $pt_metafields );
            }
            if ( !empty( $pt_counters ) )
            {
                authorship_update_post_counters( $pt_counters );
            }
        }
    }
}
add_action( 'authorship/options', 'authorship_pro_maybe_update_post_data', 10, 2 );