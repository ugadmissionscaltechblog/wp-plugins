<div id="caltech-blogger-box">
    <div class="caltech-blogger-box__avatar-wrapper">
        <?php if ( $print_avatar ) : ?>
            <img src="<?php echo esc_url( $author['avatar'] ); ?>" alt="<?php echo esc_attr( $author['display_name'] ); ?>" loading="lazy">
        <?php endif; ?>
    </div>
    <div class="caltech-blogger-box__text">
        <div class="caltech-blogger-widget-container">
            <?php if ( $print_name ) : ?>
                <h5>
                    <?php if ( ! empty( $author['posts_url'] ) ) : ?>
                        <a href="<?php echo esc_url( $link_url ) ?>" >
                            <strong><?php echo esc_html( $author['display_name'] ); ?></strong>
                        </a>
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