<?php
?>

<!-- MOLONGUI AUTHORSHIP PLUGIN <?php echo MOLONGUI_AUTHORSHIP_VERSION ?> -->
<!-- <?php echo MOLONGUI_AUTHORSHIP_WEB ?> -->
<ul <?php echo isset( $atts['list_id'] ) ? 'id="'.$atts['list_id'].'"' : ''; ?> class="m-a-list <?php echo $atts['list_class']; ?>" data-list-layout="flat" <?php echo ( $add_microdata ? 'itemscope itemtype="https://schema.org/Person"' : '' ); ?> <?php echo $atts['list_atts']; ?>>
    <?php foreach ( $authors as $author ) : ?>
        <li class="m-a-list-item" <?php echo ( $add_microdata ? 'itemprop="name"' : '' ); ?>>
            <i class="m-a-icon-<?php echo $atts['list_icon']; ?>"></i>
            &nbsp;&nbsp;
            <?php if ( $atts['name_link'] ) : ?><a href="<?php echo esc_url( $author['archive'] ); ?>" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?>><?php endif; ?>
                <?php echo $author['name']; ?>
            <?php if ( $atts['name_link'] ) : ?></a><?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>