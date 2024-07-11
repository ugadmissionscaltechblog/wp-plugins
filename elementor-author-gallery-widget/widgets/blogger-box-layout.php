<div id="caltech-blogger-box">
    <div class="caltech-blogger-box__avatar-wrapper">
        <?php if ( $print_avatar ) { ?>
            <img <?php $this->print_render_attribute_string( 'avatar' ); ?>>
        <?php } ?>
    </div>
    <div class="caltech-blogger-box__text">
        <div class="caltech-blogger-widget-container">
            <?php if ( $print_name ) : ?>
                <h5>
                    <span>
                        <<?php \Elementor\Utils::print_validated_html_tag( $link_tag ); ?> <?php $this->print_render_attribute_string( 'author_link' ); ?> style="color: #ff6c0c;">
                            <strong><?php \Elementor\Utils::print_unescaped_internal_string( $author['display_name'] ); ?></strong>
                        </<?php \Elementor\Utils::print_validated_html_tag( $link_tag ); ?>>
                    </span>
                </h5>
            <?php endif; ?>
            <p></p>
            <?php if ( $print_bio ) : ?>
                <p class="p1"><?php \Elementor\Utils::print_unescaped_internal_string( $author['bio'] ); ?></p>
            <?php endif; ?>
            <p></p>
        </div>
    </div>
</div>