<?php
defined( 'ABSPATH' ) or exit;
$bio = get_user_meta( $user->ID, 'description', true );
?>

<div id="molongui-bio-info">

    <h3><?php _e( 'Biographical Info' ); ?></h3>
    <table class="form-table" role="presentation">
        <tbody>

        <tr class="user-description-wrap">
            <th><label for="molongui_author_long_bio"><?php _e( 'Full Bio', 'molongui-authorship' ); ?></label></th>
            <td>
                <?php wp_editor( $bio, 'molongui_author_long_bio', array( 'default_editor' => 'tinymce', 'media_buttons' => false, 'textarea_rows' => 7, 'editor_css' => '<style>#wp-description-editor-tools .wp-switch-editor {box-sizing:content-box;}</style>' ) ); ?>
                <p class="description"><?php _e( "Biographical information to be shown publicly on several places on your site.", 'molongui-authorship' ); ?></p>
            </td>
        </tr>

        <?php if ( authorship_is_feature_enabled( 'box' ) ) : ?>
            <tr class="user-description-wrap">
                <th><label for="molongui_author_short_bio"><?php _e( 'Short Bio', 'molongui-authorship' ); ?></label></th>
                <td>
                    <?php wp_editor( get_the_author_meta( 'molongui_author_short_bio', $user->ID ), 'molongui_author_short_bio', array( 'default_editor' => 'tinymce', 'media_buttons' => false, 'teeny' => true, 'textarea_rows' => 3, 'editor_css' => '<style>#wp-molongui_author_short_bio-editor-tools .wp-switch-editor {box-sizing:content-box;}</style>' ) ); ?>
                    <p class="description"><?php _e( "Concise biographical paragraph you can display on author boxes instead of full bio to keep them slim.", 'molongui-authorship' ); ?></p>
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>

</div>