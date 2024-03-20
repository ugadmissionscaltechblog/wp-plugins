<?php
defined( 'ABSPATH' ) or exit;
function authorship_pro_options_footer_items()
{
    $docs_url = 'https://www.molongui.com/docs/' . MOLONGUI_AUTHORSHIP_NAME;

    return array
    (
        'links' => array
        (
            array
            (
                'label'   => __( "Pro", 'molongui-authorship-pro' ) . ( defined( 'MOLONGUI_AUTHORSHIP_PRO_VERSION' ) ? ' '.MOLONGUI_AUTHORSHIP_PRO_VERSION : '' ),
                'tip'     => __( "See changelog", 'molongui-authorship-pro' ),
                'prefix'  => '<span class="m-page-footer__version">',
                'suffix'  => '</span>',
                'href'    => $docs_url.'/changelog/changelog-pro-version/',
                'target'  => '_blank',
                'display' => true,
            ),
            array
            (
                'label'   => __( "Free", 'molongui-authorship-pro' ) . " " . MOLONGUI_AUTHORSHIP_VERSION,
                'tip'     => __( "See changelog", 'molongui-authorship-pro' ),
                'prefix'  => '<span class="m-page-footer__version">',
                'suffix'  => '</span>',
                'href'    => $docs_url.'/changelog/changelog-free-version/',
                'target'  => '_blank',
                'display' => true,
            ),
            array
            (
                'label'   => __( "Docs", 'molongui-authorship-pro' ),
                'tip'     => __( "Read the plugin documentation", 'molongui-authorship-pro' ),
                'prefix'  => '',
                'suffix'  => '',
                'href'    => $docs_url,
                'target'  => '_blank',
                'display' => true,
            ),
            array
            (
                'label'   => __( "Help", 'molongui-authorship-pro' ),
                'tip'     => __( "Click to get help", 'molongui-authorship-pro' ),
                'prefix'  => '',
                'suffix'  => '',
                'href'    => admin_url( 'admin.php?page=molongui-authorship-help' ),
                'target'  => '_self',
                'display' => true,
            ),
            array
            (
                'label'   => __( "Chat", 'molongui-authorship-pro' ),
                'tip'     => __( "How can we help?", 'molongui-authorship-pro' ),
                'prefix'  => '',
                'suffix'  => '',
                'href'    => 'https://www.tidiochat.com/chat/foioudbu7xqepgvwseufnvhcz6wkp7am',
                'target'  => '_blank',
                'display' => true,
            ),
        ),
    );
}
add_filter( 'authorship/options_footer_items', 'authorship_pro_options_footer_items' );