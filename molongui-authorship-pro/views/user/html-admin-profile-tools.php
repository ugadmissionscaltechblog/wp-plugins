<?php
defined( 'ABSPATH' ) or exit;
?>

<div id="molongui-author-tools">

    <h3><?php _e( "Author Tools", 'molongui-authorship' ); ?></h3>

    <table class="form-table" role="presentation">
        <tbody>

        <!-- Archive Author -->
        <tr class="user-m-archive-author-wrap">
            <th scope="row"><label for="molongui_author_archived"><?php _e( "Archive Author", 'molongui-authorship' ); ?></label></th>
            <td><label for="molongui_author_archived"><input type="checkbox" name="molongui_author_archived" id="molongui_author_archived" value="1" <?php checked( get_the_author_meta( 'molongui_author_archived', $user->ID ) ); disabled( $user_box_display, 'hide' ); ?>> <?php _e( "Check this box to archive this author so he/she won't be displayed as an eligible author for your posts. Won't be listed in the authors dropdown in your edit-post screen.", 'molongui-authorship' ); ?></label></td>
        </tr>

        <!-- Convert User to Guest -->
        <tr class="user-m-convert-to-guest-wrap convert">
            <th scope="row"><label><?php _e( "Convert to Guest", 'molongui-authorship-pro' ); ?></label></th>
            <td>
                <a class="button button-large" href="admin.php?action=authorship_user_to_guest&amp;user=<?php echo $user->ID; ?>" data-user-id="<?php echo $user->ID; ?>"><?php _e( "Convert", 'molongui-authorship-pro' ); ?></a>
            </td>
        </tr>

        </tbody>
    </table>

</div>