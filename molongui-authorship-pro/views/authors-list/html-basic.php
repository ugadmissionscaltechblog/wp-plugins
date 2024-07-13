<?php
?>

<!-- MOLONGUI AUTHORSHIP PLUGIN <?php echo MOLONGUI_AUTHORSHIP_VERSION ?> -->
<!-- <?php echo MOLONGUI_AUTHORSHIP_WEB ?> -->
<div <?php echo isset( $atts['list_id'] ) ? 'id="'.$atts['list_id'].'"' : ''; ?> class="m-a-list <?php echo $atts['list_class']; ?>" data-list-layout="basic" <?php echo ( $add_microdata ? 'itemscope itemtype="https://schema.org/Person"' : '' ); ?> <?php echo $atts['list_atts']; ?>>
    <?php foreach ( $authors as $author ) : ?>
        <div class="m-a-list-item">

            <!-- Author profile picture -->
            <div class="m-a-list-item-img">
                <?php if ( $atts['avatar_link'] ) : ?><a href="<?php echo esc_url( $author['archive'] ); ?>"><?php endif; ?>
                    <?php echo $author['img']; ?>
                <?php if ( $atts['avatar_link'] ) : ?></a><?php endif; ?>
            </div>

            <!-- Author data -->
            <div class="m-a-list-item-data">

                <!-- Author name -->
                <div class="m-a-list-item-name" <?php echo ( $add_microdata ? 'itemprop="name"' : '' ); ?>>
                    <?php if ( $atts['name_link'] ) : ?><a href="<?php echo esc_url( $author['archive'] ); ?>" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?>><?php endif; ?>
                        <?php echo $author['name']; ?>
                    <?php if ( $atts['name_link'] ) : ?></a><?php endif; ?>
                </div>

                <!-- Author job -->
                <div class="m-a-list-item-job">
                    <span <?php echo ( $add_microdata ? 'itemprop="jobTitle"' : '' ); ?>><?php echo $author['job']; ?></span>
                    <?php if ( $author['job'] && $author['company'] ) echo ' ' . ( $options['author_box_meta_at'] ? $options['author_box_meta_at'] : __('at', 'molongui-authorship-pro' ) ) . ' '; ?>
                    <span <?php echo ( $add_microdata ? 'itemprop="worksFor" itemscope itemtype="https://schema.org/Organization"' : '' ); ?>>
                        <?php if ( $author['company_link'] ) echo '<a href="' . esc_url( $author['company_link'] ) . '" target="_blank" '.( $add_microdata ? 'itemprop="url"' : '' ).'>'; ?>
                        <span <?php echo ( $add_microdata ? 'itemprop="name"' : '' ); ?>><?php echo $author['company']; ?></span>
                        <?php if ( $author['company_link'] ) echo '</a>'; ?>
                    </span>
                </div>

                <?php if ( $atts['show_post_count'] ) : ?>
                <!-- Posts count -->
                <div class="m-a-list-item-post-count">
                    <?php
                    $total_count = 0;
                    foreach ( $author['post_count'] as $post_type => $post_count )
                    {
                        if ( empty( $post_count ) ) continue;

                        if ( 'all' === $atts['show_post_count'] or in_array( $post_type, (array)$atts['show_post_count'] ) )
                        {
                            if ( $atts['show_post_count_total'] )
                            {
                                $total_count += $post_count;
                            }
                            else
                            {
                                $post_type = get_post_type_object( $post_type );
                                printf( _n( '%1$s %2$s ', '%1$s %3$s ', $post_count, 'molongui-authorship-pro' ), number_format_i18n( $post_count ), strtolower( $post_type->labels->singular_name ), strtolower( $post_type->labels->name ) );
                            }
                        }
                    }
                    if ( $atts['show_post_count_total'] )
                    {
                        $entry_tag   = apply_filters( 'authorship_pro/author_list/entry_tag', 'entry' );
                        $entries_tag = apply_filters( 'authorship_pro/author_list/entries_tag', 'entries' );
                        printf( _n( '%1$s %2$s ', '%1$s %3$s ', $total_count, 'molongui-authorship-pro' ), number_format_i18n( $total_count ), $entry_tag, $entries_tag );
                    }
                    ?>
                </div>
                <?php endif; ?>

                <?php if ( $author['mail'] and $author['show_meta_mail'] and !$author['show_meta_mail'] ) : ?>
	                <?php
                        if ( isset( $options['encode_email'] ) and $options['encode_email'] )
                        {
                            $email  = molongui_ascii_encode( $author['mail'] );
                            $e_href = '&#109;&#97;&#105;&#108;&#116;&#111;&#58;'.$email;
                        }
                        else
                        {
                            $email  = $author['mail'];
                            $e_href = 'mailto:'.$author['mail'];
                        }
	                ?>
                    <!-- Author mail -->
                    <div class="m-a-list-item-mail">
                            <a href="<?php echo $e_href; ?>" target="_top" <?php echo ( $add_microdata ? 'itemprop="email"' : '' ); ?>><?php echo $email; ?></a>
                    </div>
                <?php endif; ?>

	            <?php if ( $author['phone'] and $author['show_meta_phone'] and !$author['show_social_phone'] ) : ?>
		            <?php
                        if( isset( $options['encode_phone'] ) and $options['encode_phone'] )
                        {
                            $phone  = molongui_ascii_encode( $author['phone'] );
                            $p_href = '&#116;&#101;&#108;&#58;'.$phone;
                        }
                        else
                        {
                            $phone  = $author['phone'];
                            $p_href = 'tel:'.$author['phone'];
                        }
		            ?>
                    <!-- Author phone -->
                    <div class="m-a-list-item-phone">
                        <a href="<?php echo $p_href; ?>" target="_top" <?php echo ( $add_microdata ? 'itemprop="telephone"' : '' ); ?>><?php echo $phone; ?></a>
                    </div>
	            <?php endif; ?>

                <?php if ( $author['web'] and !$author['show_social_web'] ) : ?>
                    <!-- Author link -->
                    <div class="m-a-list-item-link">
                            <a href="<?php echo esc_url( $author['web'] ); ?>" target="_blank" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?>><?php echo ( $options['author_box_meta_web'] ? $options['author_box_meta_web'] : __( "Website", 'molongui-authorship-pro' ) ); ?></a>
                    </div>
                <?php endif; ?>

                <!-- Author social media -->
                <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-socialmedia.php'; ?>

                <?php if ( $atts['show_bio'] ) : ?>
                    <!-- Author bio -->
                    <?php include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-bio.php'; ?>
                <?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>
</div>