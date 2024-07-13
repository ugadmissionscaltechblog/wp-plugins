<?php

use Molongui\Authorship\Post;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'authorship_pro_bulk_edit_add_post_custom_fields' ) )
{
    function authorship_pro_bulk_edit_add_post_custom_fields( $column_name, $post_type )
    {
        $post_types = molongui_enabled_post_screens( MOLONGUI_AUTHORSHIP_PREFIX, 'all' );
        if ( !in_array( $post_type, $post_types ) ) return;
        if ( $column_name == 'molongui-author' )
        {
            wp_nonce_field( 'molongui_authorship_bulk_edit_nonce', 'molongui_authorship_bulk_edit_nonce' );

            ?>
            <br class="clear"/>

            <fieldset class="inline-edit-col-right" style="margin-bottom:20px;">
                <div class="inline-edit-col">
                <!--<h4><?php _e( "Authorship Data", 'molongui-authorship-pro' ); ?></h4>-->
                    <div class="inline-edit-group wp-clearfix">
                        <label class="inline-edit-authors alignleft" style="width: 100%; max-width: 100%;">
                            <span class="title"><?php _e( "Authors", 'molongui-authorship-pro' ); ?></span>
                            <style>.selectr-container { margin-top:4px !important; }</style>
                            <div id="molongui-author-selectr" style="margin-left: 6em;">
                                <?php echo authorship_dropdown_authors( 'authors', array( 'selected' => '' ) ); ?>
                            </div>
                        </label>
                    </div>
                </div>
            </fieldset>
            <?php
        }
        if ( $column_name == 'molongui-box' )
        {
            wp_nonce_field( 'molongui_authorship_bulk_edit_nonce', 'molongui_authorship_bulk_edit_nonce' );

            ?>
        <!--<br class="clear"/>-->
            <fieldset class="inline-edit-col-right">
                <div class="inline-edit-col">
                    <div class="inline-edit-group wp-clearfix">
                        <label class="inline-edit-box-display alignleft">
                            <span class="title"><?php _e( "Author Box", 'molongui-authorship-pro' ); ?></span>
                            <select name="_molongui_author_box_display">
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
}
if ( !function_exists( 'authorship_pro_bulk_edit_submit_post_custom_fields' ) )
{
    function authorship_pro_bulk_edit_submit_post_custom_fields()
    {
        global $post_type;
        $post_types = molongui_enabled_post_screens( MOLONGUI_AUTHORSHIP_PREFIX, 'all' );
        if ( !in_array( $post_type, $post_types ) ) return;
        wp_enqueue_script( 'jquery' );
        ?>

        <script type="text/javascript">
            jQuery(function ($)
            {
                $(document).on('click', '#doaction', function ()
                {
                    if ($('#bulk-action-selector-top').val() === 'edit' || $('#bulk-action-selector-bottom').val() === 'edit')
                    {
                        var $bulk_row = $('#bulk-edit');
                        <?php if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) : ?>
                            $tags = $bulk_row.find('.inline-edit-tags').parent().clone();
                            $bulk_row.find('.inline-edit-tags').remove();

                        <?php else : ?>
                            $tags = $bulk_row.find('.inline-edit-tags-wrap').parent().clone();
                            $bulk_row.find('.inline-edit-tags-wrap').remove();

                        <?php endif; ?>
                        $tags.removeClass('inline-edit-col-left').addClass('inline-edit-col-right').insertAfter('.inline-edit-col-center').children('.inline-edit-col').remove();
                        var authorList = $bulk_row.find('ul#molongui_authors');
                        var authorSelect = document.getElementById('_molongui_author');
                        var container = document.getElementById('molongui-author-selectr');
                        if (container.hasChildNodes()) container.removeChild(container.firstElementChild);
                        container.prepend(authorSelect);
                        $.molonguiInitAuthorSelector(authorSelect, authorList, "<?php _e( '&mdash; No Change &mdash;' ); ?>");
                        authorList.children().remove();
                    }
                });
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
                    var $authors_key = '';

                    <?php if ( authorship_is_feature_enabled( 'multi' ) ) : ?>

                    $authors_key = 'molongui_authors';
                    var $post_authors = new Array();
                    $bulk_row.find('[name="molongui_authors[]"]').each(function ()
                    {
                        $post_authors.push($(this).val());
                    });

                    <?php else : ?>

                    $authors_key = '_molongui_author';
                    var $post_authors = '';
                    $bulk_row.find('[name="_molongui_author"]').each(function ()
                    {
                        $post_authors = $(this).val();
                    });

                    <?php endif; ?>

                    var $box_display = $bulk_row.find('select[name="_molongui_author_box_display"]').val();
                    $.ajax(
                    {
                        url   : ajaxurl, // this is a variable that WordPress has already defined for us.
                        type  : 'POST',
                        async : false,
                        cache : false,
                        data  :
                        {
                            action         : 'authorship_save_bulk_edit_fields',
                            post_ids       : $post_ids,
                            [$authors_key] : $post_authors,
                            box_display    : $box_display,
                            nonce          : $('#molongui_authorship_bulk_edit_nonce').val(),
                        }
                    });
                });
            });
        </script>
        <?php
    }
}
if ( !function_exists( 'authorship_pro_bulk_edit_save_post_custom_fields' ) )
{
    function authorship_pro_bulk_edit_save_post_custom_fields()
    {
        if ( !wp_verify_nonce( $_POST['nonce'], 'molongui_authorship_bulk_edit_nonce' ) ) wp_die();
        if ( empty( $_POST['post_ids'] ) ) wp_die();
        if ( !empty( $_POST['molongui_post_authors'] ) or !empty( $_POST['_molongui_author'] ) )
        {
            foreach ( $_POST['post_ids'] as $id )
            {
                Post::update_authors( $_POST['molongui_post_authors'], $id, '', get_current_user_id() );
            }
        }
        if ( !empty( $_POST['box_display'] ) )
        {
            foreach ( $_POST['post_ids'] as $id ) update_post_meta( $id, '_molongui_author_box_display', $_POST['box_display'] );
        }
        wp_die();
    }
}