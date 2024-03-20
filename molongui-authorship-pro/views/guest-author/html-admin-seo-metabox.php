<?php
defined( 'ABSPATH' ) or exit;

?>
<div class="molongui-metabox">

    <!-- Author Archive Settings -->
    <label class="m-title" for=""><strong><?php _e( 'Author Page', 'molongui-authorship-pro' ); ?></strong></label>

    <!-- No index author archive page -->
    <div class="m-field">
        <div class="input-wrap">
            <input type="checkbox" id="_molongui_guest_author_noindex" name="_molongui_guest_author_noindex" value="1" <?php checked( $guest_author_noindex, 1 ); ?>>
            <label class="checkbox-label" for="_molongui_guest_author_noindex"><?php _e( "Do not allow search engines to show this author's archives in search results", 'molongui-authorship-pro' ); ?></label>
        </div>
    </div>

</div>