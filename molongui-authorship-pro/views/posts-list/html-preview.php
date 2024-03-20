<?php
?>

<!-- <?php echo strtoupper( MOLONGUI_AUTHORSHIP_PRO_TITLE .' '. MOLONGUI_AUTHORSHIP_PRO_VERSION ); ?> -->
<!-- <?php echo MOLONGUI_AUTHORSHIP_WEB ?> -->
<div class="<?php echo $atts['class']; ?>">
    <ul class="m-a-posts-list" data-list-layout="preview">
        <?php foreach ( $posts as $post ) : ?>
            <li class="m-a-posts-list-item" <?php echo ( $add_microdata ? 'itemscope itemtype="http://schema.org/CreativeWork"' : '' ); ?>>

                <!-- Post thumb -->
                <?php if ( has_post_thumbnail( $post->ID ) ) : ?>
                    <div class="m-a-posts-list-item-thumb">
                        <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
                            <?php echo get_the_post_thumbnail( $post->ID, 'full', $attr = ( $add_microdata ? array( 'itemprop' => 'thumbnailUrl' ) : array() ) ) ?>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Post data -->
                <div class="m-a-posts-list-item-data">

                    <!-- Post title -->
                    <div class="m-a-posts-list-item-title">
                        <a class="molongui-remove-underline" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?> href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
                            <span <?php echo ( $add_microdata ? 'itemprop="headline"' : '' ); ?>><?php echo $post->post_title; ?></span>
                        </a>
                    </div>

                    <!-- Post meta -->
                    <div class="m-a-posts-list-item-meta">

                        <?php if ( $atts['show_byline'] ) : ?>
                        <div <?php echo ( $add_microdata ? 'itemprop="author" itemscope itemtype="http://schema.org/Person"' : '' ); ?>>
                            <?php echo apply_filters( 'authorship_pro/sc/posts/by', __( 'By', 'molongui-authorship-pro' ) ) . ' '; ?>
                            <a href="<?php echo esc_url( $author->get_url() ); ?>" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?>>
                                <span <?php echo ( $add_microdata ? 'itemprop="name"' : '' ); ?>><?php echo $author->get_name(); ?></span>
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if ( $atts['show_date'] ) : ?>
                            <span><?php echo apply_filters( 'authorship_pro/sc/posts/on', __( 'on', 'molongui-authorship-pro' ) ) . ' '; ?></span>
                            <span <?php echo ( $add_microdata ? 'itemprop="datePublished"' : '' ); ?>><?php echo get_the_date( '', $post->ID ); ?></span>
                        <?php endif; ?>

                    </div>

                    <?php if ( $atts['show_excerpt'] ) : ?>
                        <!-- Post excerpt -->
                        <div class="m-a-posts-list-item-excerpt" <?php echo ( $add_microdata ? 'itemprop="abstract"' : '' ); ?>>
                            <?php
                            $the_post    = get_post( $post->ID );
                            $the_excerpt = has_excerpt( $post->ID ) ? $the_post->post_excerpt : $the_post->post_content;
                            $excerpt_length = 25; // (int) apply_filters( 'excerpt_length', '55' );
                            $the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) );

                            $excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
                            $the_excerpt  = wp_trim_words( $the_excerpt, $excerpt_length, $excerpt_more );

                            echo $the_excerpt;
                            ?>
                        </div>
                    <?php endif; ?>

                </div>

            </li>
        <?php endforeach; ?>
    </ul>
</div>