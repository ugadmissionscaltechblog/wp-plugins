<?php

?>

<div <?php echo isset( $atts['list_id'] ) ? 'id="'.$atts['list_id'].'"' : ''; ?> class="m-a-list <?php echo $atts['list_class']; ?>" data-list-layout="box" <?php echo $atts['list_atts']; ?>>
    <?php foreach ( $authors as $author ) : ?>
        <?php
            if ( $author['box'] == 'hide' ) continue;
        ?>
        <div class="m-a-list-item">
		    <?php echo do_shortcode( '[molongui_author_box box_margin="30" show_headline="no" id="'.$author['id'].'" type="'.$author['type'].'" force_display=true]' );?>
        </div>
    <?php endforeach; ?>
</div>