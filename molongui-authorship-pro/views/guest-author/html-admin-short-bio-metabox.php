<?php
defined( 'ABSPATH' ) or exit;
$guest_author_short_bio = get_post_meta( $post->ID, '_molongui_guest_author_short_bio', true );
?>

<div class="molongui-metabox">

    <?php wp_nonce_field( 'authorship_guest_short_bio', 'authorship_guest_short_bio' ); ?>
    <div class="m-field">
        <p class="m-description">
            <?php _e( 'Provide a short description that can be displayed on the author box instead of the full author biography. On author archive pages full bio is displayed, if your theme supports it.', 'molongui-authorship' ); ?>
        </p>
        <?php wp_editor( $guest_author_short_bio, '_molongui_guest_author_short_bio', array( 'media_buttons' => false, 'teeny' => true, 'editor_height' => 150, 'textarea_rows' => 5 ) ); ?>
    </div>

</div>