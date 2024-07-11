<div id="caltech-blogger-box">
    <div class="caltech-blogger-box__avatar-wrapper">
        <?php if ( $print_avatar ) : ?>
            <img alt="<?php echo esc_attr( $author['display_name'] ); ?>" src="<?php echo esc_url( $author['avatar'] ); ?>">
        <?php endif; ?>
    </div>
    <div class="caltech-blogger-box__text">
        <div class="caltech-blogger-widget-container">
            <?php if ( $print_name ) : ?>
                <h5>
                    <?php if ( ! empty( $author['posts_url'] ) ) : ?>
                        <<?php echo esc_html( $link_tag ); ?> <?php $this->print_render_attribute_string( 'author_link' ); ?> style="color: #ff6c0c;">
                            <strong><?php echo esc_html( $author['display_name'] ); ?></strong>
                        </<?php echo esc_html( $link_tag ); ?>>
                    <?php else : ?>
                        <span>
                            <strong><?php echo esc_html( $author['display_name'] ); ?></strong>
                        </span>
                    <?php endif; ?>
                </h5>
            <?php endif; ?>
            <p></p>
            <?php if ( $print_bio ) : ?>
                <p class="p1"><?php echo wp_kses_post( $author['bio'] ); ?></p>
            <?php endif; ?>
            <p></p>
        </div>
    </div>
</div>