<?php
defined( 'ABSPATH' ) or exit;
add_filter( 'authorship/guest/bulk_actions/remove_edit', '__return_false' );
if ( !function_exists( 'authorship_pro_bulk_edit_add_guest_title_field' ) )
{
    function authorship_pro_bulk_edit_add_guest_title_field()
    {
        global $pagenow, $post_type;

        if ( 'edit.php' == $pagenow and $post_type == MOLONGUI_AUTHORSHIP_CPT ) add_post_type_support( $post_type, 'title' );
    }
    add_action( 'admin_head', 'authorship_pro_bulk_edit_add_guest_title_field' );
}
if ( !function_exists( 'authorship_pro_bulk_edit_add_guest_custom_fields' ) )
{
    function authorship_pro_bulk_edit_add_guest_custom_fields( $column_name, $post_type )
    {
        if ( ( $post_type !== MOLONGUI_AUTHORSHIP_CPT ) or !current_user_can( 'edit_posts' ) ) return;
        if ( $column_name == 'guestDisplayBox' )
        {
            wp_nonce_field( 'molongui_authorship_bulk_edit_nonce', 'molongui_authorship_bulk_edit_nonce' );
            ?>
            <fieldset class="inline-edit-col-right">
                <div class="inline-edit-col">
                    <div class="inline-edit-group wp-clearfix">
                        <label class="inline-edit-status alignleft">
                            <span class="title"><?php esc_html_e( "Author Box", 'molongui-authorship-pro' ); ?></span>
                            <select name="_molongui_guest_author_box_display">
                                <option value=""><?php _e( "&mdash; No Change &mdash;" ); ?></option>
                                <option value="default"><?php _e( "Default" ); ?></option>
                                <option value="show"><?php _e( "Show" ); ?></option>
                                <option value="hide"><?php _e( "Hide" ); ?></option>
                            </select>
                        </label>
                    </div>
                </div>
            </fieldset>
            <?php
        }
    }
    add_action( 'bulk_edit_custom_box', 'authorship_pro_bulk_edit_add_guest_custom_fields', 10, 2 );
}
if ( !function_exists( 'authorship_pro_submit_bulk_edit_guest_custom_fields' ) )
{
    function authorship_pro_submit_bulk_edit_guest_custom_fields()
    {
        global $post_type;
        if ( $post_type != MOLONGUI_AUTHORSHIP_CPT ) return;
        wp_enqueue_script( 'jquery' );
        ?>

        <script type="text/javascript">
            jQuery(function ($)
            {
                $(document).on('click', '#bulk_edit', function ()
                {
                    var $bulk_row = $('#bulk-edit');
                    var $post_ids = new Array();
                    <?php if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) : ?>

                    $bulk_row.find('#bulk-titles').children().each(function ()
                    {
                        $post_ids.push($(this).attr('id').replace(/^(ttle)/i, ''));
                    });

                    <?php else : ?>

                    $bulk_row.find('#bulk-titles-list .ntdelitem button').each(function ()
                    {
                        $post_ids.push($(this).attr('id').replace(/^(_)/i, ''));
                    });

                    <?php endif; ?>
                    var $box_display = $bulk_row.find('select[name="_molongui_guest_author_box_display"]').val();
                    $.ajax(
                    {
                        url   : ajaxurl, // this is a variable that WordPress has already defined for us.
                        type  : 'POST',
                        async : false,
                        cache : false,
                        data  :
                        {
                            action      : 'bulk_edit_save_guest_custom_fields',
                            post_ids    : $post_ids,
                            box_display : $box_display,
                            nonce       : $('#molongui_authorship_bulk_edit_nonce').val(),
                        }
                    });
                });
            });
        </script>
        <?php
    }
    add_action( 'admin_footer','authorship_pro_submit_bulk_edit_guest_custom_fields' );
}
if ( !function_exists( 'authorship_pro_bulk_edit_save_guest_custom_fields' ) )
{
    function authorship_pro_bulk_edit_save_guest_custom_fields()
    {
        if ( !wp_verify_nonce( $_POST['nonce'], 'molongui_authorship_bulk_edit_nonce' ) ) wp_die();
        if ( empty( $_POST['post_ids'] ) ) wp_die();
        if ( empty( $_POST['box_display'] ) ) wp_die();
        foreach ( $_POST['post_ids'] as $id ) update_post_meta( $id, '_molongui_guest_author_box_display', $_POST['box_display'] );

        wp_die();
    }
    add_action( 'wp_ajax_bulk_edit_save_guest_custom_fields', 'authorship_pro_bulk_edit_save_guest_custom_fields' );
}