<?php

$data = apply_filters( 'authorship_pro/authors_list_data_items', array
(
    'avatar',
    'name',
    'position',
    'email',
    'phone',
    'web',
    'post_count',
    'bio',
    'social'
));
$list_styles  = '';
$list_styles .= empty( $atts['grid-edge-gap'] ) ? '' : 'padding: '.$atts['grid-edge-gap'].';';
$list_styles .= empty( $atts['grid-row-gap'] ) ? '' : 'grid-row-gap: '.$atts['grid-row-gap'].';';
$list_styles .= empty( $atts['grid-column-gap'] ) ? '' : 'grid-column-gap: '.$atts['grid-column-gap'].';';
$list_styles .= empty( $atts['columns'] ) ? '' : 'grid-template-columns: repeat('.$atts['columns'].', minmax(0, 1fr));';

$item_styles  = '';
$item_styles .= empty( $atts['grid-item-padding'] ) ? '' : 'padding: ' . ( authorship_input_has_units( $atts['grid-item-padding'] ) ? $atts['grid-item-padding'].';' : $atts['grid-item-padding'].'px;' );
$item_styles .= empty( $atts['grid-item-color'] ) ? '' : 'background-color: '.$atts['grid-item-color'].';';
$item_styles .= empty( $atts['grid-item-border-width'] ) ? '' : 'border-width: ' . ( authorship_input_has_units( $atts['grid-item-border-width'] ) ? $atts['grid-item-border-width'].';' : $atts['grid-item-border-width'].'px;' );
$item_styles .= empty( $atts['grid-item-border-style'] ) ? '' : 'border-style: '.$atts['grid-item-border-style'].';';
$item_styles .= empty( $atts['grid-item-border-color'] ) ? '' : 'border-color: '.$atts['grid-item-border-color'].';';
$item_styles .= empty( $atts['grid-item-border-radius'] ) ? '' : 'border-radius: ' . ( authorship_input_has_units( $atts['grid-item-border-radius'] ) ? $atts['grid-item-border-radius'].';' : $atts['grid-item-border-radius'].'px;' );

if ( !empty( $list_styles ) or !empty( $item_styles ) )
{
    add_action( 'wp_print_footer_scripts', function() use  ( $list_styles, $item_styles )
    {
        ?>
        <style>
            <?php echo empty( $list_styles ) ? '' : '.m-a-list[data-list-layout="grid"] {'.$list_styles.'}'; ?>
            <?php echo empty( $item_styles ) ? '' : '.m-a-list[data-list-layout="grid"] .m-a-list-item {'.$item_styles.'}'; ?>
        </style>
        <?php
    }, 11 );
}

?>

<!-- MOLONGUI AUTHORSHIP PLUGIN <?php echo MOLONGUI_AUTHORSHIP_VERSION ?> -->
<!-- <?php echo MOLONGUI_AUTHORSHIP_WEB ?> -->
<div <?php echo isset( $atts['list_id'] ) ? 'id="'.$atts['list_id'].'"' : ''; ?> class="m-a-list <?php echo $atts['list_class']; ?>" data-list-layout="grid" <?php echo ( $add_microdata ? 'itemscope itemtype="https://schema.org/Person"' : '' ); ?> <?php echo $atts['list_atts']; ?>>
    <?php foreach ( $authors as $author ) : ?>
        <div class="m-a-list-item">
            <?php
            foreach ( $data as $info )
            {
                switch ( $info )
                {
                    case 'avatar':

                        ?>
                        <div class="m-a-list-author__avatar"><a href="<?php echo esc_url( $author['archive'] ); ?>"><?php echo $author['img']; ?></a></div>
                        <?php

                    break;

                    case 'name':

                        ?>
                        <div class="m-a-list-author__name" <?php echo ( $add_microdata ? 'itemprop="name"' : '' ); ?>><a href="<?php echo esc_url( $author['archive'] ); ?>" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?>><?php echo $author['name']; ?></a></div>
                        <?php

                    break;

                    case 'position':

                        ?>
                        <div class="m-a-list-author__position">
                            <span <?php echo ( $add_microdata ? 'itemprop="jobTitle"' : '' ); ?>><?php echo $author['job']; ?></span>
                            <?php if ( $author['job'] && $author['company'] ) echo ' ' . ( $options['author_box_meta_at'] ? $options['author_box_meta_at'] : __( "at", 'molongui-authorship-pro' ) ) . ' '; ?>
                            <span <?php echo ( $add_microdata ? 'itemprop="worksFor" itemscope itemtype="https://schema.org/Organization"' : '' ); ?>>
                                <?php if ( $author['company_link'] ) echo '<a href="' . esc_url( $author['company_link'] ) . '" target="_blank" '.( $add_microdata ? 'itemprop="url"' : '' ).'>'; ?>
                                    <span <?php echo ( $add_microdata ? 'itemprop="name"' : '' ); ?>><?php echo $author['company']; ?></span>
                                <?php if ( $author['company_link'] ) echo '</a>'; ?>
                            </span>
                        </div>
                        <?php

                    break;

                    case 'email':

                        if ( $author['mail'] and $author['show_meta_mail'] and !$author['show_meta_mail'] )
                        {
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

                            <div class="m-a-list-author__email">
                                <a href="<?php echo $e_href; ?>" target="_top" <?php echo ( $add_microdata ? 'itemprop="email"' : '' ); ?>><?php echo $email; ?></a>
                            </div>
                        <?php }

                    break;

                    case 'phone':

                        if ( $author['phone'] and $author['show_meta_phone'] and !$author['show_social_phone'] )
                        {
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

                            <div class="m-a-list-author__phone">
                                <a href="<?php echo $p_href; ?>" target="_top" <?php echo ( $add_microdata ? 'itemprop="telephone"' : '' ); ?>><?php echo $phone; ?></a>
                            </div>
                        <?php }

                    break;

                    case 'web':

                        if ( $author['web'] and !$author['show_social_web'] ) : ?>

                        <div class="m-a-list-author__link">
                            <a href="<?php echo esc_url( $author['web'] ); ?>" target="_blank" <?php echo ( $add_microdata ? 'itemprop="url"' : '' ); ?>><?php echo ( $options['author_box_meta_web'] ? $options['author_box_meta_web'] : __( "Website", 'molongui-authorship-pro' ) ); ?></a>
                        </div>

                        <?php endif;

                    break;

                    case 'post_count':

                        if ( $atts['show_post_count'] ) : ?>
                        <div class="m-a-list-author__post-count">
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
                        <?php endif;

                    break;

                    case 'bio':

                        if ( $atts['show_bio'] )
                        {
                            include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-bio.php';
                        }

                    break;

                    case 'social':

                        include MOLONGUI_AUTHORSHIP_DIR . 'views/author-box/parts/html-socialmedia.php';

                    break;
                }
            } ?>
        </div>
    <?php endforeach; ?>
</div>