<?php
?>

<!-- <?php echo strtoupper( MOLONGUI_AUTHORSHIP_PRO_TITLE .' '. MOLONGUI_AUTHORSHIP_PRO_VERSION ); ?> -->
<!-- <?php echo MOLONGUI_AUTHORSHIP_WEB ?> -->
<div class="<?php echo $atts['class']; ?>">
    <ul class="m-a-posts-list" data-list-layout="plain">
        <?php foreach ( $posts as $post ) : ?>
            <li class="m-a-posts-list-item <?php echo $atts['list_divider'] ? 'm-a-posts-list-item-divider' : '' ?>" <?php echo ( $add_microdata ? 'itemscope itemtype="http://schema.org/CreativeWork"' : '' ); ?>>

                <?php if ( $add_microdata ) : ?>
                    <div class="molongui-display-none" itemprop="author" itemscope itemtype="http://schema.org/Person">
                        <div itemprop="name"><?php echo $author->get_name(); ?></div>
                        <div itemprop="url"><?php echo esc_url( $author->get_url() ); ?></div>
                    </div>
                <?php endif; ?>

                <?php if ( $atts['list_icon'] != 'none' ) : ?><i class="m-a-icon-<?php echo $atts['list_icon']; ?>"></i>&nbsp;&nbsp;<?php endif; ?>
                <a class="molongui-remove-underline" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?> href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" style="<?php echo ( !empty( $atts['link_color'] ) ? 'color:'.$atts['link_color'].';' : '' ); ?> <?php echo ( !empty( $atts['link_decoration'] ) ? 'text-decoration:'.$atts['link_decoration'].';' : '' ); ?>">
                    <span <?php echo ( $add_microdata ? 'itemprop="headline"' : '' ); ?>><?php echo $post->post_title; ?></span>
                </a>

            </li>
        <?php endforeach; ?>
    </ul>
</div>