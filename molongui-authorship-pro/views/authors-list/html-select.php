<?php

use Molongui\Authorship\Includes\Author;
?>

<!-- MOLONGUI AUTHORSHIP PLUGIN <?php echo MOLONGUI_AUTHORSHIP_VERSION ?> -->
<!-- <?php echo MOLONGUI_AUTHORSHIP_WEB ?> -->

<?php if ( $atts['dev_mode'] ) : ?>

    <select <?php echo isset( $atts['list_id'] ) ? 'id="'.$atts['list_id'].'" name="'.$atts['list_id'].'"' : ''; ?> <?php echo isset( $atts['list_class'] ) ? 'class="'.$atts['list_class'].'"' : ''; ?> <?php echo $atts['list_atts']; ?>>
        <option value="<?php $atts['default_option_value']; ?>"><?php echo $atts['default_option_label']; ?></option>
        <?php foreach( $authors as $author ) : ?>
            <option value="<?php echo $author['type'].'-'.$author['id']; ?>" data-author-id="<?php echo $author['id']; ?>" data-author-type="<?php echo $author['type']; ?>"><?php echo $author['name']; ?></option>
		<?php endforeach; ?>
    </select>

<?php else : ?>

    <select <?php echo isset( $atts['select_id'] ) ? 'id="'.$atts['select_id'].'" name="'.$atts['select_id'].'"' : ''; ?> <?php echo isset( $atts['select_class'] ) ? 'class="'.$atts['select_class'].'"' : ''; ?> <?php echo isset( $atts['select_atts'] ) ? $atts['select_class'] : ''; ?> onchange="javascript: this.value && (location.href = this.value);">
        <option value="<?php $atts['default_option_value']; ?>"><?php echo $atts['default_option_label']; ?></option>
		<?php foreach( $authors as $author ) : ?>
            <?php $author_class = new Author( $author['id'], $author['type'] ); ?>
            <option value="<?php echo $author_class->get_url(); ?>" data-author-id="<?php echo $author['id']; ?>" data-author-type="<?php echo $author['type']; ?>"><?php echo $author['name']; ?></option>
		<?php endforeach; ?>
    </select>

<?php endif; ?>