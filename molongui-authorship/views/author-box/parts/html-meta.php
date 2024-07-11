<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( empty( $options['author_box_meta_show'] ) )
{
    return;
}

$meta      = '';
$nofollow  = ( $options['add_nofollow'] ? 'rel="nofollow"' : '' );
$separator = apply_filters( 'molongui_authorship/author_meta_separator', sprintf( '&nbsp;%s&nbsp;', '<span class="m-a-box-meta-divider">'.$options['author_box_meta_divider'].'</span>' ) );
if ( !empty( $author['job'] ) )
{
    $author_job = '<span ' . ( $add_microdata ? 'itemprop="jobTitle"' : '' ) . '>' . esc_html( $author['job'] ) . '</span>';
}
if ( !empty( $author['company'] ) )
{
    $author_company = sprintf(
        '%s%s%s%s%s'
        , '<span ' . ( $add_microdata ? 'itemprop="worksFor" itemscope itemtype="https://schema.org/Organization"' : '' ) . '>'
        , $author['company_link'] ? '<a href="' . esc_url( $author['company_link'] ) . '" target="_blank" '.( $add_microdata ? 'itemprop="url"' : '' ). $nofollow . '>' : ''
        , '<span ' . ( $add_microdata ? 'itemprop="name"' : '' ) . '>' . esc_html( $author['company'] ) . '</span>'
        , $author['company_link'] ? '</a>' : ''
        , '</span>'
    );
}
if ( !empty( $author['phone'] ) )
{
    $author_phone = '<a href="tel:'.esc_attr( $author['phone'] ).'"'. ( $add_microdata ? ' itemprop="telephone"' : '' ) . ' content="'.esc_attr( $author['phone'] ).'" '.$nofollow.'>' . esc_html( $author['phone'] ) . '</a>';
    $author_phone = apply_filters( 'authorship/box/meta/phone', $author_phone, $author['phone'], $add_microdata, $nofollow );
}
if ( !empty( $author['mail'] ) )
{
    $author_email = '<a href="mailto:'.esc_attr( $author['mail'] ).'" target="_top"'. ( $add_microdata ? ' itemprop="email"' : '' ) . ' content="'.esc_attr( $author['mail'] ).'" '.$nofollow.'>' . esc_html( $author['mail'] ) . '</a>';
    $author_email = apply_filters( 'authorship/box/meta/email', $author_email, $author['mail'], $add_microdata, $nofollow );

}
if ( !empty( $author['web'] ) )
{
    $author_web = '<a href="' . esc_attr( $author['web'] ) . '" target="_blank" '. $nofollow . '>'
                  . '<span class="m-a-box-string-web">' . apply_filters( 'authorship/box/meta/web', ( $options['author_box_meta_web'] ? $options['author_box_meta_web'] : __( "Website", 'molongui-authorship' ) ), $author ) . '</span>'
                  . '</a>';
}
if ( !empty( $author_job ) )
{
    $meta .= $author_job;
}
if ( !empty( $author_company ) )
{
    if ( !empty( $author_job ) )
    {
        $meta .= sprintf( '%s%s%s'
            , '&nbsp;'
            , '<span class="m-a-box-string-at">' . apply_filters( 'authorship/box/meta/at', ( $options['author_box_meta_at'] ? esc_attr( $options['author_box_meta_at'] ) : __( "at", 'molongui-authorship' ) ), $author ).'</span>'
            , '&nbsp;'
        );
    }
    $meta .= $author_company;
}
if ( !empty( $author_phone ) and ( $options['author_box_meta_show_phone'] or $author['show_meta_phone'] ) )
{
    if ( !empty( $author_job ) or !empty( $author_company ) )
    {
        $meta .= $separator;
    }
    $meta .= $author_phone;
}
if ( !empty( $author_email ) and ( $options['author_box_meta_show_email'] or $author['show_meta_mail'] ) )
{
    if ( !empty( $author_job ) or !empty( $author_company ) or !empty( $author_phone ) )
    {
        $meta .= $separator;
    }
    $meta .= $author_email;
}
if ( !empty( $author_web ) )
{
    if ( !empty( $author_job ) or !empty( $author_company ) or !empty( $author_phone ) or !empty( $author_email ) )
    {
        $meta .= $separator;
    }
    $meta .= $author_web;
}

?>

<div class="m-a-box-item m-a-box-meta">
    <?php echo $meta; ?>
</div>