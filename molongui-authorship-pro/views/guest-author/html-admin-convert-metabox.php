<?php
defined( 'ABSPATH' ) or exit;
molongui_enqueue_sweetalert();
?>

<div class="molongui-metabox">

    <?php wp_nonce_field( 'authorship_convert_guest', 'authorship_convert_guest' ); ?>
    <div class="m-field convert">
        <label class="m-title" for="_molongui_guest_author_box_display"><strong><?php _e( "Convert to User", 'molongui-authorship-pro' ); ?></strong></label>
        <p class="m-description">
            <?php _e( "Convert this guest author to a registered WP user with just 1-click. Current guest will be removed and a new user created. Posts authorship will be kept.", 'molongui-authorship-pro' ); ?>
        </p>
        <a class="button button-large" href="admin.php?action=authorship_guest_to_user&amp;post=<?php echo $post->ID; ?>"><?php _e( "Convert", 'molongui-authorship-pro' ); ?></a>
    </div>

</div>