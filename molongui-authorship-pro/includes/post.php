<?php

namespace Molongui\Authorship\Pro;

use Molongui\Authorship\Common\Modules\Settings;
use Molongui\Authorship\Common\Utils\Helpers;
use Molongui\Authorship\Common\Utils\WP;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Post extends \Molongui\Authorship\Post
{
    public function __construct()
    {
        add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit_add_custom_fields' ), 0, 2 );
        add_action( 'admin_footer', array( $this, 'bulk_edit_submit_custom_fields' ) );
        add_action( 'wp_ajax_authorship_save_bulk_edit_fields', array( $this, 'bulk_edit_save_custom_fields' ) );
    }
    public function bulk_edit_add_custom_fields( $column_name, $post_type )
    {
        if ( !authorship_byline_takeover() )
        {
            return;
        }
        $post_types = molongui_enabled_post_screens( MOLONGUI_AUTHORSHIP_PREFIX, 'all' );
        if ( !in_array( $post_type, $post_types ) )
        {
            return;
        }
        if ( $column_name == 'molongui-author' ) : ?>

            <br class="clear"/>
            <fieldset class="inline-edit-col-right" style="margin-bottom:20px;">
                <div class="inline-edit-col">
                    <!--<h4><?php _e( "Authorship Data", 'molongui-authorship-pro' ); ?></h4>-->
                    <div class="inline-edit-group wp-clearfix">
                        <label class="inline-edit-authors alignleft" style="width: 100%; max-width: 100%;">
                            <span class="title"><?php authorship_is_feature_enabled( 'multi' ) ? _e( "Authors", 'molongui-authorship-pro' ) : _e( "Author" ); ?></span>
                            <?php Post::author_selector( null, 'bulk' ); ?>
                            <?php wp_nonce_field( 'molongui_authorship_bulk_edit', 'molongui_authorship_bulk_edit_nonce' ); ?>
                        </label>
                    </div>
                </div>
            </fieldset>

        <?php
        elseif ( $column_name == 'molongui-box' ) : ?>

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
                <?php wp_nonce_field( 'molongui_authorship_bulk_edit', 'molongui_authorship_bulk_edit_nonce' ); ?>
            </fieldset>

        <?php endif;
    }
    public function bulk_edit_submit_custom_fields()
    {
        global $post_type;
        $post_types = molongui_enabled_post_screens( MOLONGUI_AUTHORSHIP_PREFIX, 'all' );
        if ( !in_array( $post_type, $post_types ) )
        {
            return;
        }
        wp_enqueue_script( 'jquery' );

        ob_start();
        ?>

        <script type="text/javascript">
            jQuery(function($)
            {
                $(document).on('click', '#doaction', function()
                {
                    if ($('#bulk-action-selector-top').val() === 'edit' || $('#bulk-action-selector-bottom').val() === 'edit')
                    {
                        const $bulk_editor = $('#bulk-edit');

                        <?php if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) : ?>
                            $tags = $bulk_editor.find('.inline-edit-tags').parent().clone();
                            $bulk_editor.find('.inline-edit-tags').remove();

                        <?php else : ?>
                            $tags = $bulk_editor.find('.inline-edit-tags-wrap').parent().clone();
                            $bulk_editor.find('.inline-edit-tags-wrap').remove();

                        <?php endif; ?>
                        $tags.removeClass('inline-edit-col-left').addClass('inline-edit-col-right').insertAfter('.inline-edit-col-center').children('.inline-edit-col').remove();
                        if ( 'function' === typeof window.molonguiAuthorshipInitAuthorSelector )
                        {
                            window.molonguiAuthorshipInitAuthorSelector( $bulk_editor.find('#molongui-post-authors') );
                        }
                        else
                        {
                            console.error( 'Global function molonguiAuthorshipInitAuthorSelector is not defined' );
                        }
                        $('.molongui-post-authors__input').attr( "placeholder", '<?php echo html_entity_decode( esc_html__( "&mdash; No Change &mdash;" ) ); ?>' );
                    }
                });
                $(document).on('click', '#bulk_edit', function()
                {
                    const $bulk_editor = $('#bulk-edit');
                    const $post_ids = new Array();
                    <?php if ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) : ?>

                        $bulk_editor.find('#bulk-titles').children().each(function ()
                        {
                            $post_ids.push($(this).attr('id').replace(/^(ttle)/i, ''));
                        });

                    <?php else : ?>

                        $bulk_editor.find('#bulk-titles-list .ntdelitem button').each(function ()
                        {
                            $post_ids.push($(this).attr('id').replace(/^(_)/i, ''));
                        });

                    <?php endif; ?>
                    const $post_authors = new Array();
                    $bulk_editor.find('[name="molongui_post_authors[]"]').each(function ()
                    {
                        $post_authors.push($(this).val());
                    });

                    const $box_display = $bulk_editor.find('select[name="_molongui_author_box_display"]').val();
                    $.ajax(
                    {
                        url   : ajaxurl, // this is a variable that WordPress has already defined for us.
                        type  : 'POST',
                        async : false,
                        cache : false,
                        data  :
                        {
                            action                : 'authorship_save_bulk_edit_fields',
                            nonce                 : $('#molongui_authorship_bulk_edit_nonce').val(),
                            post_ids              : $post_ids,
                            post_type             : <?php echo json_encode( $post_type ); ?>, // 'json_encode' to safely output PHP variables in JavaScript. It takes care of adding quotes for strings.
                            molongui_post_authors : $post_authors, // keep the same 'molongui_post_authors' key
                            box_display           : $box_display,
                        },
                        success: function(response)
                        {
                            console.log(response);
                        },
                        error: function(xhr, status, error)
                        {
                            console.log('Error: ', error);
                        }
                    });
                });
            });
        </script>
        <?php

        echo Helpers::minify_js( ob_get_clean() );
    }
    public function bulk_edit_save_custom_fields()
    {
        if ( !WP::verify_nonce( 'molongui_authorship_bulk_edit', 'nonce' ) )
        {
            echo wp_json_encode( array( 'result' => 'error', 'message' => __( "Missing or invalid nonce.", 'molongui-authorship-pro' ), 'function' => __FUNCTION__ ) );
            wp_die();
        }
        if ( empty( $_POST['post_ids'] ) )
        {
            echo wp_json_encode( array( 'result' => 'error', 'message' => __( "Missing post IDs...", 'molongui-authorship-pro' ), 'function' => __FUNCTION__ ) );
            wp_die();
        }

        foreach ( $_POST['post_ids'] as $post_id )
        {
            if ( !current_user_can( 'edit_post', $post_id ) )
            {
                continue;
            }
            if ( !empty( $_POST['molongui_post_authors'] ) )
            {
                Post::update_authors( $_POST['molongui_post_authors'], $post_id, $_POST['post_type'], get_current_user_id() );
            }
            if ( !empty( $_POST['box_display'] ) )
            {
                update_post_meta( $post_id, '_molongui_author_box_display', sanitize_text_field( $_POST['box_display'] ) );
            }
        }
        echo wp_json_encode( array( 'result' => 'success', 'message' => __( "Custom fields for the Molongui Authorship plugin saved.", 'molongui-authorship-pro' ), 'function' => __FUNCTION__ ) );
        wp_die();
    }

} // class
new Post();