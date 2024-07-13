<?php

use Molongui\Authorship\Common\Modules\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
add_filter( 'authorship/options/display_banners', '__return_false' );
add_filter( '_authorship/options/post_type/disabled', '__return_false' );
function authorship_pro_get_enhanced_search_markup()
{
    $enhanced_search = array();
    $enhanced_search[] = array
    (
        'display' => true,
        'deps'    => '',
        'search'  => '',
        'type'    => 'toggle',
        'class'   => '',
        'default' => false,
        'id'      => 'enable_search_by_author',
        'title'   => '',
        'desc'    => '',
        'help'    => sprintf( __( "%sImprove visitors user experience allowing them to search content by author name.%s %sBy default, WordPress search only looks into post content.%s %sEnable this setting so searching by author name returns content authored by that author.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
        'label'   => __( "Enhance default search functionality allowing visitors to search content by author name", 'molongui-authorship-pro' ),
    );

    return $enhanced_search;
}
add_filter( '_authorship/options/search_by_name', 'authorship_pro_get_enhanced_search_markup' );
function authorship_pro_guest_search_markup()
{
    $enhanced_search = array();
    $enhanced_search[] = array
    (
        'display'  => true,
        'advanced' => false,
        'deps'     => 'guest_authors',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => '',
        'default'  => false,
        'id'       => 'enable_guests_in_search',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sGive more visibility to your guests including their archive page on search results.%s %sIf a search by author name is made, not only that author's posts will be displayed but also that author archive page.%s %sOnly applies to guest authors. Doesn't work with registered users.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>', '<p><strong>', '</strong></p>' ),
        'label'    => __( "Include guest author pages in search results if searched string matches the guest author name", 'molongui-authorship-pro' ),
    );

    return $enhanced_search;
}
add_filter( '_authorship/options/guest_search_markup', 'authorship_pro_guest_search_markup' );
function authorship_pro_guests_rest_api()
{
    $rest_api = array();
    $rest_api[] = array
    (
        'display'  => true,
        'advanced' => false,
        'deps'     => 'guest_authors',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => 'hidden',
        'default'  => false,
        'id'       => 'enable_guests_in_api',
        'title'    => '',
        'desc'     => '',
        'help'     => '',
        'label'    => sprintf( __( "Make Guest Author custom post type available at %s%s%s", 'molongui-authorship-pro' ), '<code>', get_rest_url( null, '/wp/v2/guests/' ), '</code>' ),
    );

    return $rest_api;
}
add_filter( '_authorship/options/guests_rest_api', 'authorship_pro_guests_rest_api' );
if ( !function_exists( 'authorship_pro_get_permissions_markup' ) )
{
    function authorship_pro_get_permissions_markup()
    {
        $user_roles   = array();
        $user_roles[] = array
        (
            'display' => true,
            'deps'    => '',
            'search'  => '',
            'type'    => 'dropdown',
            'atts'    => array
            (
                'search' => true,
                'multi'  => true,
            ),
            'class'   => '',
            'default' => 'author,contributor',
            'id'      => 'permissions_hide_others_posts',
            'title'   => '',
            'desc'    => __( "Hide other authors' posts for these user roles:", 'molongui-authorship-pro' ),
            'help'    => array
            (
                'text' => sprintf( __( "%sBy default, WordPress users in the admin area can see all the Posts on the site, regardless of whether they are the author.%s %sThis is not a problem for many sites. After all, most Posts on most sites are publicly available – there's no need to hide them.%s %sHowever, in some situations, site owners don't want authors to see other users' posts to keep the Dashboard as clean as possible.%s" ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
                'link' => '',
            ),
            'label'   => '',
            'options' => authorship_pro_get_user_role_options(),
        );

        return $user_roles;
    }
    add_filter( '_authorship/options/permissions/markup', 'authorship_pro_get_permissions_markup' );
}
function authorship_pro_multi_rest_api()
{
    $rest_api = array();
    $rest_api[] = array
    (
        'display'  => true,
        'advanced' => false,
        'deps'     => 'enable_multi_authors',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => 'hidden',
        'default'  => false,
        'id'       => 'enable_authors_in_api',
        'title'    => '',
        'desc'     => '',
        'help'     => '',
        'label'    => __( "Add an 'authors' field to the post object returned by the REST API.", 'molongui-authorship-pro' ),
    );

    return $rest_api;
}
add_filter( '_authorship/options/multi_rest_api', 'authorship_pro_multi_rest_api' );
if ( !function_exists( 'authorship_pro_get_user_roles_markup' ) )
{
    function authorship_pro_get_user_roles_markup()
    {
        $user_roles   = array();
        $user_roles[] = array
        (
            'display' => true,
            'deps'    => '',
            'search'  => '',
            'type'    => 'dropdown',
            'atts'    => array
            (
                'search' => true,
                'multi'  => true,
            ),
            'class'   => '',
            'default' => '',
            'id'      => 'user_roles',
            'title'   => '',
            'desc'   => __( "Select which user roles the plugin will take into account to populate authors dropdown selector, authors list and other stuff. Custom user roles are supported.", 'molongui-authorship-pro' ),
            'help'    => '',
            'label'   => '',
            'options' => authorship_pro_get_user_role_options(),
        );

        return $user_roles;
    }
    add_filter( '_authorship/options/user_roles/markup', 'authorship_pro_get_user_roles_markup' );
}
if ( !function_exists( 'authorship_pro_get_interface_markup' ) )
{
    function authorship_pro_get_interface_markup()
    {
        $interface = array();
        $interface[] = array
        (
            'display' => true,
            'deps'    => 'guest_authors',
            'search'  => '',
            'type'    => 'inline-dropdown',
            'class'   => '',
            'default' => 'top',
            'id'      => 'guests_menu_level',
            'title'   => '',
            'desc'    => '',
            'help'    => sprintf( __( "%sSelect where in the admin menu to add a new item to manage guest authors.%s %sIn order to this setting to take effect, a page reload is required.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'label'   => sprintf( __( "Add %sGuest Authors%s link as %s", 'molongui-authorship-pro' ), '<code>', '</code>', '{input}' ),
            'options' => array
            (
                'top' => array
                (
                    'icon'  => '',
                    'label' => __( "a top level menu item", 'molongui-authorship-pro' ),
                ),
                'users.php' => array
                (
                    'icon'  => '',
                    'label' => sprintf( __( "a sub-menu item to the %sUsers%s menu", 'molongui-authorship-pro' ), '<code>', '</code>' ),
                ),
                'edit.php' => array
                (
                    'icon'  => '',
                    'label' => sprintf( __( "a sub-menu item to the %sPosts%s menu", 'molongui-authorship-pro' ), '<code>', '</code>' ),
                ),
                'edit.php?post_type=page' => array
                (
                    'icon'  => '',
                    'label' => sprintf( __( "a sub-menu item to the %sPages%s menu", 'molongui-authorship-pro' ), '<code>', '</code>' ),
                ),
            ),
        );

        return $interface;
    }
    add_filter( '_authorship/options/interface/markup', 'authorship_pro_get_interface_markup' );
}
if ( !function_exists( 'authorship_pro_get_categories_markup' ) )
{
    function authorship_pro_get_categories_markup()
    {
        $display = array();
        $display[] = array
        (
            'display'  => true,
            'advanced' => true,
            'deps'     => '',
            'search'   => '',
            'type'     => 'dropdown',
            'atts'     => array
            (
                'search' => true,
                'multi'  => true,
            ),
            'class'    => '',
            'default'  => '',
            'id'       => 'hide_on_categories',
            'title'    => '',
            'desc'     => sprintf( __( "%sHide%s the author box on these %spost categories%s:", 'molongui-authorship-pro' ), '<strong>', '</strong>', '<strong>', '</strong>' ),
            'help'     => array
            (
                'text' => sprintf( __( "%sThis setting might be overridden by post configuration, so exceptions can be handled on a per post basis.%s", 'molongui-authorship-pro' ), '<p>', '</p>' ),
                'link' => 'https://www.molongui.com/docs/molongui-authorship/author-box/display-settings/',
            ),
            'label'    => '',
            'options'  => molongui_post_categories( true, false ),
        );

        return $display;
    }
    add_filter( '_authorship/options/display/categories/markup', 'authorship_pro_get_categories_markup' );
}
if ( !function_exists( 'authorship_pro_get_hook_priority_markup' ) )
{
    function authorship_pro_get_hook_priority_markup()
    {
        $placement = array();
        $placement[] = array
        (
            'display'     => true,
            'advanced'    => true,
            'deps'        => '',
            'search'      => '',
            'type'        => 'inline-number',
            'default'     => 11,
            'placeholder' => '11',
            'min'         => 1,
            'max'         => '',
            'step'        => 1,
            'class'       => '',
            'id'          => 'order',
            'title'       => '',
            'desc'        => '',
            'help'        => sprintf( __( "%sOther plugins may also add their stuff to post content, so the author box might be displayed somewhere different than expected. Making the plugin to add the author box before that third-party content (lowering the priority number) should move the box up, while adding it later (increasing the priority number) should move the box down.%s %sA value below 10 may cause issues with your content.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'label'       => sprintf( __( "Add the author box at a priority of %s %s%sChange only if author box is not displayed where expected%s", 'molongui-authorship-pro' ), '{input}', '&emsp;', '<code>', '</code>' ),
        );

        return $placement;
    }
    add_filter( '_authorship/options/hook_priority/markup', 'authorship_pro_get_hook_priority_markup' );
}
if ( !function_exists( 'authorship_pro_get_spam_protect_markup' ) )
{
    function authorship_pro_get_spam_protect_markup()
    {
        $spam_protect = array();
        $spam_protect[] = array
        (
            'display'  => true,
            'advanced' => false,
            'deps'     => '',
            'search'   => '',
            'type'     => 'toggle',
            'class'    => 'hidden',
            'default'  => false,
            'id'       => 'encode_email',
            'title'    => '',
            'desc'     => '',
            'help'     => '',
            'label'    => __( "Encode e-mail addresses to prevent them getting spammed.", 'molongui-authorship-pro' ),
        );
        $spam_protect[] = array
        (
            'display'  => true,
            'advanced' => false,
            'deps'     => '',
            'search'   => '',
            'type'     => 'toggle',
            'class'    => 'hidden',
            'default'  => false,
            'id'       => 'encode_phone',
            'title'    => '',
            'desc'     => '',
            'help'     => '',
            'label'    => __( "Encode phone numbers to prevent them getting annoying scam calls and texts.", 'molongui-authorship-pro' ),
        );

        return $spam_protect;
    }
    add_filter( '_authorship/options/spam_protect/markup', 'authorship_pro_get_spam_protect_markup' );
}
if ( !function_exists( 'authorship_pro_get_box_info_markup' ) )
{
    function authorship_pro_get_box_info_markup()
    {
        $box_info = array();
        $box_info[] = array
        (
            'display' => true,
            'deps'    => '',
            'search'  => '',
            'type'    => 'inline-dropdown',
            'class'   => '',
            'default' => '_blank',
            'id'      => 'author_box_bio_source',
            'title'   => '',
            'desc'    => '',
            'help'    => '',
            'label'   => sprintf( __( "Show %s bio when available", 'molongui-authorship' ), '{input}' ),
            'options' => array
            (
                'short' => array
                (
                    'icon'  => '',
                    'label' => __( "short", 'molongui-authorship' ),
                ),
                'full' => array
                (
                    'icon'  => '',
                    'label' => __( "full", 'molongui-authorship' ),
                ),
            ),
        );

        return $box_info;
    }
}
if ( !function_exists( 'authorship_pro_get_related_posts_markup' ) )
{
    function authorship_pro_get_related_posts_markup()
    {
        $related_posts = array();
        $related_posts[] = array
        (
            'display'     => true,
            'advanced'    => false,
            'deps'        => 'show_related',
            'search'      => '',
            'type'        => 'inline-number',
            'default'     => 4,
            'placeholder' => '4',
            'min'         => 1,
            'max'         => '',
            'step'        => 1,
            'class'       => '',
            'id'          => 'author_box_related_count',
            'title'       => '',
            'desc'        => '',
            'help'        => '',
            'label'       => sprintf( __( "Display %s entries as related posts.", 'molongui-authorship-pro' ), '{input}' ),
        );
        $related_posts[] = array
        (
            'display'  => true,
            'advanced' => false,
            'deps'     => 'show_related',
            'search'   => '',
            'type'     => 'inline-dropdown',
            'class'    => '',
            'default'  => 'date',
            'id'       => 'author_box_related_orderby',
            'title'    => '',
            'desc'     => '',
            'help'     => sprintf( __( "%sThe criteria to sort related entries.%s", 'molongui-authorship-pro' ), '<p>', '</p>' ),
            'label'    => sprintf( __( "Sort related entries by %s", 'molongui-authorship-pro' ),  '{input}' ),
            'options'  => array
            (
                'title' => array
                (
                    'icon'  => '',
                    'label' => __( "title", 'molongui-authorship-pro' ),
                ),
                'date' => array
                (
                    'icon'  => '',
                    'label' => __( "date on which the post was written", 'molongui-authorship-pro' ),
                ),
                'modified' => array
                (
                    'icon'  => '',
                    'label' => __( "date on which the post was last modified", 'molongui-authorship-pro' ),
                ),
                'comment_count' => array
                (
                    'icon'  => '',
                    'label' => __( "comment count", 'molongui-authorship-pro' ),
                ),
                'rand' => array
                (
                    'icon'  => '',
                    'label' => __( "random order", 'molongui-authorship-pro' ),
                ),
            ),
        );
        $related_posts[] = array
        (
            'display'  => true,
            'advanced' => false,
            'deps'     => 'show_related',
            'search'   => '',
            'type'     => 'inline-dropdown',
            'class'    => '',
            'default'  => 'desc',
            'id'       => 'author_box_related_order',
            'title'    => '',
            'desc'     => '',
            'help'     => sprintf( __( "%sThe order by which to list related entries.%s", 'molongui-authorship-pro' ), '<p>', '</p>' ),
            'label'    => sprintf( __( "Sort related entries in %s order", 'molongui-authorship-pro' ),  '{input}' ),
            'options'  => array
            (
                'asc'  => array
                (
                    'icon'  => '',
                    'label' => __( "ascending", 'molongui-authorship-pro' ),
                ),
                'desc' => array
                (
                    'icon'  => '',
                    'label' => __( "descending", 'molongui-authorship-pro' ),
                ),
            ),
        );
        $related_posts[] = array
        (
            'display'  => true,
            'advanced' => true,
            'deps'     => 'show_related',
            'search'   => '',
            'type'     => 'dropdown',
            'atts'     => array
            (
                'search' => true,
                'multi'  => true,
            ),
            'class'    => '',
            'default'  => '',
            'id'       => 'author_box_related_post_types',
            'title'    => '',
            'desc'     => sprintf( __( "Post types to retrieve %sas related entries%s:", 'molongui-authorship-pro' ), '<strong>', '</strong>' ),
            'help'     => sprintf( __( "%sPost types listed as related items.%s %sOnly those post types where plugin functionality is enabled on are listed. If you have just enabled some an they are not listed here, save settings and refresh this page.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'label'    => '',
            'options'  => authorship_get_post_types(), //molongui_supported_post_types( MOLONGUI_AUTHORSHIP_NAME, 'all', true ),
        );

        return $related_posts;
    }
    add_filter( '_authorship/options/related_posts/markup', 'authorship_pro_get_related_posts_markup' );
}
if ( !function_exists( 'authorship_pro_get_byline_modifiers_markup' ) )
{
    function authorship_pro_get_byline_modifiers_markup()
    {
        $modifiers = array();
        $modifiers[] = array
        (
            'display'     => true,
            'deps'        => '',
            'search'      => '',
            'type'        => 'inline-text',
            'placeholder' => __( "Written by ", 'molongui-authorship-pro' ),
            'default'     => '',
            'class'       => '',
            'id'          => 'byline_prefix',
            'title'       => '',
            'desc'        => '',
            'help'        => array
            (
                'text'    => sprintf( __( "%sDoesn't remove nor replace any modifier added by your theme, like the widely used 'By'.%s %sHTML markup is accepted, so you can add your own styles and custom elements.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
                'link'    => 'https://www.molongui.com/docs/molongui-authorship/byline/byline-modifiers/',
            ),
            'label'       => sprintf( __( "At the very beginning of each byline, prepend this string: %s", 'molongui-authorship-pro' ), '{input}' ),

        );
        $modifiers[] = array
        (
            'display'     => true,
            'deps'        => '',
            'search'      => '',
            'type'        => 'inline-text',
            'placeholder' => __( " et al.", 'molongui-authorship-pro' ),
            'default'     => '',
            'class'       => '',
            'id'          => 'byline_suffix',
            'title'       => '',
            'desc'        => '',
            'help'        => array
            (
                'text'    => sprintf( __( "%sDoesn't remove nor replace any modifier added by your theme or any other plugin.%s %sHTML markup is accepted, so you can add your own styles and custom elements.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
                'link'    => 'https://www.molongui.com/docs/molongui-authorship/byline/byline-modifiers/',
            ),
            'label'       => sprintf( __( "At the end of each byline, append this string: %s", 'molongui-authorship-pro' ), '{input}' ),
        );

        return $modifiers;
    }
    add_filter( '_authorship/options/byline/modifiers/markup', 'authorship_pro_get_byline_modifiers_markup' );
}
function authorship_pro_get_guest_archive_markup()
{
    $guest_archive = array();
    $guest_archive[] = array
    (
        'display'  => true,
        'advanced' => false,
        'deps'     => 'guest_authors',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => '',
        'default'  => true,
        'id'       => 'guest_pages',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sGuest Archives are pages listing all posts from a given guest author. This is the same functionality WordPress provides for registered users but extended to guest authors.%s %sIf enabled, guest names become a link to these pages.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
        'label'    => __( "Enable Guest Author Archives.", 'molongui-authorship-pro' ),
    );
    $guest_archive[] = array
    (
        'display'  => true,
        'advanced' => true,
        'deps'     => 'guest_authors',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => '',
        'default'  => false,
        'id'       => 'guest_archive_include_pages',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "By default, only %sposts%s are listed.", 'molongui-authorship-pro' ), '<code>', '</code>' ),
        'label'    => sprintf( __( "Include %spages%s authored by the guest author in the archive.", 'molongui-authorship-pro' ),'<code>', '</code>' ),
    );
    $guest_archive[] = array
    (
        'display'  => true,
        'advanced' => true,
        'deps'     => 'guest_authors',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => '',
        'default'  => false,
        'id'       => 'guest_archive_include_cpts',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "By default, only %sposts%s are listed.", 'molongui-authorship-pro' ), '<code>', '</code>' ),
        'label'    => sprintf( __( "Include custom post types where plugin functionality is enabled in the archive.", 'molongui-authorship-pro' ),'<code>', '</code>' ),
    );
    $guest_archive[] = array
    (
        'display'     => true,
        'advanced'    => true,
        'deps'        => 'guest_authors',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => '',
        'default'     => '',
        'class'       => 'inline',
        'id'          => 'guest_archive_permalink',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sEasily change permalink structure for guest author archives pages.%s %sYou can provide any string but %smake sure not to overlap any other existing URL on your site.%s%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '<strong>', '</strong>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "Add a custom slug to guest author page URL: %s%s%s", 'molongui-authorship-pro' ), '<code>'.get_site_url( null, '/' ).'</code>', '{input}', '<code>'.'/author/john-doe'.'</code>' ),
    );
    $guest_archive[] = array
    (
        'display'     => true,
        'advanced'    => false,
        'deps'        => 'guest_authors',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => __( "author", 'molongui-authorship-pro' ),
        'default'     => __( "author", 'molongui-authorship-pro' ),
        'class'       => 'inline',
        'id'          => 'guest_archive_base',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sBy default, author archive pages are accessible at: %s%s %sYou can easily change that %sauthor%s part with any other custom string of your choice.%s", 'molongui-authorship-pro' ), '<p>', '<code>'.get_site_url( null, '/author/' ).'</code>', '</p>', '<p>', '<code>', '</code>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "Make guest author pages accessible at: %s%s%s", 'molongui-authorship-pro' ), '<code>'.get_site_url( null, '/' ).'</code>', '{input}', '<code>'.'/john-doe'.'</code>' ),
    );
    $guest_archive[] = array
    (
        'display'     => true,
        'advanced'    => true,
        'deps'        => 'guest_authors',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => "template_name.php",
        'default'     => '',
        'class'       => 'inline',
        'id'          => 'guest_archive_tmpl',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sWordPress takes the %sauthor.php%s, %sarchive.php%s or %sindex.php%s template to display author archives.%s %sOverrule that providing an existing alternative template to use instead.%s", 'molongui-authorship-pro' ), '<p>', '<code>', '</code>', '<code>', '</code>', '<code>', '</code>', '</p>', '<p>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "Force author pages to use this template: %s", 'molongui-authorship-pro' ),  '{input}' ),
    );
    if ( version_compare( get_bloginfo( 'version' ), '5.7', '>' ) )
    {
        $guest_archive[] = array
        (
            'display'  => true,
            'advanced' => true,
            'deps'     => 'guest_authors',
            'search'   => '',
            'type'     => 'toggle',
            'class'    => '',
            'default'  => false,
            'id'       => 'guest_archive_noindex',
            'title'    => '',
            'desc'     => '',
            'help'     => sprintf( __( "%sThis option adds a %snoindex%s robots tag to your guest author pages.%s %sIf you have a SEO plugin, you might want to use it for this instead.%s", 'molongui-authorship-pro' ), '<p>', '<code>', '</code>', '</p>', '<p>', '</p>' ),
            'label'    => sprintf( __( "Prevent guest author pages to be indexed by search engines", 'molongui-authorship-pro' ),'<code>', '</code>' ),
        );
    }
    $guest_archive[] = array
    (
        'display'     => true,
        'advanced'    => false,
        'deps'        => 'guest_authors',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => __( "Articles by", 'molongui-authorship-pro' ),
        'default'     => '',
        'class'       => 'inline',
        'id'          => 'guest_archive_title_prefix',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sThis might not work for all themes or if you use a theme builder.%s %sDefaults to an empty string.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "At the very beginning of the page title, prepend this string: %s", 'molongui-authorship-pro' ),  '{input}' ),
    );
    $guest_archive[] = array
    (
        'display'     => true,
        'advanced'    => false,
        'deps'        => 'guest_authors',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => '',
        'default'     => '',
        'class'       => 'inline',
        'id'          => 'guest_archive_title_suffix',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sThis might not work for all themes.%s %sDefaults to an empty string.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "At the end of the page title, append this string: %s", 'molongui-authorship-pro' ),  '{input}' ),
    );

    return $guest_archive;
}
add_filter( '_authorship/options/archives/guest/markup', 'authorship_pro_get_guest_archive_markup' );
function authorship_pro_get_user_archive_markup()
{
    $user_archive = array();
    $user_archive[] = array
    (
        'display'  => true,
        'advanced' => false,
        'deps'     => '',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => '',
        'default'  => true,
        'id'       => 'user_archive_enabled',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sUser Archives are pages listing all posts from a given user.%s %sIf enabled, user names become a link to these pages.%s %sThis functionality is provided by WordPress by default. In some cases, you might want to disable user archives to prevent content duplicity or for security reasons.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
        'label'    => __( "Enable User Archives.", 'molongui-authorship-pro' ),
    );
    $user_archive[] = array
    (
        'display'  => true,
        'advanced' => true,
        'deps'     => '',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => '',
        'default'  => false,
        'id'       => 'user_archive_include_pages',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "By default, only %sposts%s are listed.", 'molongui-authorship-pro' ), '<code>', '</code>' ),
        'label'    => sprintf( __( "Include %spages%s authored by the user in the archive.", 'molongui-authorship-pro' ),'<code>', '</code>' ),
    );
    $user_archive[] = array
    (
        'display'  => true,
        'advanced' => true,
        'deps'     => '',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => '',
        'default'  => false,
        'id'       => 'user_archive_include_cpts',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "By default, only %sposts%s are listed.", 'molongui-authorship-pro' ), '<code>', '</code>' ),
        'label'    => sprintf( __( "Include custom post types where plugin functionality is enabled in the archive.", 'molongui-authorship-pro' ),'<code>', '</code>' ),
    );
    $user_archive[] = array
    (
        'display'     => true,
        'advanced'    => false,
        'deps'        => '',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => __( "author", 'molongui-authorship-pro' ),
        'default'     => __( "author", 'molongui-authorship-pro' ),
        'class'       => 'inline',
        'id'          => 'user_archive_base',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sBy default, author archive pages are accessible at: %s%s %sYou can easily change that %sauthor%s part with any other custom string of your choice.%s", 'molongui-authorship-pro' ), '<p>', '<code>'.get_site_url( null, '/author/' ).'</code>', '</p>', '<p>', '<code>', '</code>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "Make author pages accessible at: %s%s%s", 'molongui-authorship-pro' ), '<code>'.get_site_url( null, '/' ).'</code>', '{input}', '<code>'.'/john-doe'.'</code>' ),
    );
    $user_archive[] = array
    (
        'display'  => true,
        'advanced' => false,
        'deps'     => '',
        'search'   => '',
        'type'     => 'toggle',
        'class'    => '',
        'default'  => false,
        'id'       => 'user_archive_slug',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sThis allows you to control author permalinks by using the user %sdisplay_name%s instead of the %susername%s.%s %sSo author permalinks will look like %s instead of %s.%s %sUser display name can be easily edited at the user-edit screen.%s %sAvoiding the use of usernames in permalinks helps to improve the security of your site.%s", 'molongui-authorship-pro' ), '<p>','<code>', '</code>','<code>', '</code>', '</p>', '<p>', '<code>'.get_site_url( null, '/author/display-name' ).'</code>', '<code>'.get_site_url( null, '/author/username' ).'</code>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
        'label'    => sprintf( __( "Use author display name instead of username in author permalinks.", 'molongui-authorship-pro' ),'<code>', '</code>' ),
    );
    $user_archive[] = array
    (
        'display'     => true,
        'advanced'    => true,
        'deps'        => '',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => "template_name.php",
        'default'     => '',
        'class'       => 'inline',
        'id'          => 'user_archive_tmpl',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sWordPress takes the %sauthor.php%s, %sarchive.php%s or %sindex.php%s template to display author archives.%s %sOverrule that providing an existing alternative template to use instead.%s", 'molongui-authorship-pro' ), '<p>', '<code>', '</code>', '<code>', '</code>', '<code>', '</code>', '</p>', '<p>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "Force author pages to use this template: %s", 'molongui-authorship-pro' ),  '{input}' ),
    );
    if ( version_compare( get_bloginfo( 'version' ), '5.7', '>' ) )
    {
        $user_archive[] = array
        (
            'display'  => true,
            'advanced' => true,
            'deps'     => '',
            'search'   => '',
            'type'     => 'toggle',
            'class'    => '',
            'default'  => false,
            'id'       => 'user_archive_noindex',
            'title'    => '',
            'desc'     => '',
            'help'     => sprintf( __( "%sThis option adds a %snoindex%s robots tag to your user pages.%s %sIf you have a SEO plugin, you might want to use it for this instead.%s", 'molongui-authorship-pro' ), '<p>', '<code>', '</code>', '</p>', '<p>', '</p>' ),
            'label'    => sprintf( __( "Prevent author pages to be indexed by search engines", 'molongui-authorship-pro' ),'<code>', '</code>' ),
        );
    }
    $user_archive[] = array
    (
        'display'     => true,
        'advanced'    => false,
        'deps'        => '',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => __( "Articles by", 'molongui-authorship-pro' ),
        'default'     => '',
        'class'       => 'inline',
        'id'          => 'user_archive_title_prefix',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sThis might not work for all themes.%s %sDefaults to an empty string.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "At the very beginning of the page title, prepend this string: %s", 'molongui-authorship-pro' ),  '{input}' ),
    );
    $user_archive[] = array
    (
        'display'     => true,
        'advanced'    => false,
        'deps'        => '',
        'search'      => '',
        'type'        => 'inline-text',
        'placeholder' => '',
        'default'     => '',
        'class'       => 'inline',
        'id'          => 'user_archive_title_suffix',
        'title'       => '',
        'desc'        => '',
        'help'        => array
        (
            'text'    => sprintf( __( "%sThis might not work for all themes or if you use a theme builder.%s %sDefaults to an empty string.%s", 'molongui-authorship-pro' ), '<p>', '</p>', '<p>', '</p>' ),
            'link'    => '',
        ),
        'label'       => sprintf( __( "At the end of the page title, append this string: %s", 'molongui-authorship-pro' ),  '{input}' ),
    );
    $user_archive[] = array
    (
        'display'  => true,
        'advanced' => false,
        'deps'     => '',
        'search'   => '',
        'type'     => 'select_wp_page',
        'class'    => '',
        'default'  => '',
        'id'       => 'user_archive_redirect',
        'title'    => '',
        'desc'     => '',
        'help'     => '',
        'label'    => sprintf( __( "Redirect visitors to the %s page if they try to access to an author archive page.", 'molongui-authorship-pro' ), '{input}' ),
        'options'  => array(),
    );
    $user_archive[] = array
    (
        'display'  => true,
        'advanced' => true,
        'deps'     => '',
        'search'   => '',
        'type'     => 'inline-dropdown',
        'class'    => '',
        'default'  => 307,
        'id'       => 'user_archive_status',
        'title'    => '',
        'desc'     => '',
        'help'     => '',
        'label'    => sprintf( __( "Upon redirection let the web server return a status code of %s", 'molongui-authorship-pro' ), '{input}' ),
        'options'  => array
        (
            '301' => array
            (
                'icon'  => '',
                'label' => __( "301 - Moved Permanently", 'molongui-authorship-pro' ),
            ),
            '307' => array
            (
                'icon'  => '',
                'label' => __( "307 - Temporary Redirect", 'molongui-authorship-pro' ),
            ),
            '308' => array
            (
                'icon'  => '',
                'label' => __( "308 - Permanent Redirect", 'molongui-authorship-pro' ),
            ),
            '403' => array
            (
                'icon'  => '',
                'label' => __( "403 - Forbidden", 'molongui-authorship-pro' ),
            ),
            '404' => array
            (
                'icon'  => '',
                'label' => __( "404 - Not Found", 'molongui-authorship-pro' ),
            ),
        ),
    );

    return $user_archive;
}
add_filter( '_authorship/options/archives/user/markup', 'authorship_pro_get_user_archive_markup' );
if ( !function_exists( 'authorship_pro_get_seo_tags_markup' ) )
{
    function authorship_pro_get_seo_tags_markup()
    {
        $seo_tags = array();
        $seo_tags[] = array
        (
            'display'  => true,//authorship_is_feature_enabled( 'multi' ) or empty( $options['add_html_meta'] ),
            'advanced' => false,
            'deps'     => '',
            'search'   => '',
            'type'     => 'inline-dropdown',
            'class'    => 'hidden',
            'default'  => 'many',
            'id'       => 'multi_author_meta',
            'title'    => '',
            'desc'     => '',
            'help'     => '',
            'label'    => sprintf( __( "On co-authored posts add %s", 'molongui-authorship-pro' ),  '{input}' ),
            'options'  => array
            (
                'many' => array
                (
                    'icon'  => '',
                    'label' => __( "as many meta tag as authors the post has", 'molongui-authorship-pro' ),
                ),
                'aio' => array
                (
                    'icon'  => '',
                    'label' => __( "one meta tag containing all authors", 'molongui-authorship-pro' ),
                ),
                'main' => array
                (
                    'icon'  => '',
                    'label' => __( "one meta tag containing main author only", 'molongui-authorship-pro' ),
                ),
            ),
        );

        return $seo_tags;
    }
    add_filter( '_authorship/options/seo/tags/markup', 'authorship_pro_get_seo_tags_markup' );
}
function authorship_pro_title_tag_markup()
{
    $html_tag = array();
    $html_tag[] = array
    (
        'display'  => true,
        'advanced' => true,
        'deps'     => 'author_box',
        'search'   => '',
        'type'     => 'inline-dropdown',
        'class'    => '',
        'default'  => 'h5',
        'id'       => 'box_author_name_tag',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sSelecting a value inline with the structure of your page might improve SEO.%s", 'molongui-authorship-pro' ), '<p>', '</p>' ),
        'label'    => sprintf( __( "Use %s tag for %sauthor name%s shown within the author box", 'molongui-authorship-pro' ),  '{input}', '<strong>', '</strong>' ),
        'options'  => array
        (
            'h1' => array
            (
                'icon'  => '',
                'label' => __( "h1", 'molongui-authorship-pro' ),
            ),
            'h2' => array
            (
                'icon'  => '',
                'label' => __( "h2", 'molongui-authorship-pro' ),
            ),
            'h3' => array
            (
                'icon'  => '',
                'label' => __( "h3", 'molongui-authorship-pro' ),
            ),
            'h4' => array
            (
                'icon'  => '',
                'label' => __( "h4", 'molongui-authorship-pro' ),
            ),
            'h5' => array
            (
                'icon'  => '',
                'label' => __( "h5", 'molongui-authorship-pro' ),
            ),
            'h6' => array
            (
                'icon'  => '',
                'label' => __( "h6", 'molongui-authorship-pro' ),
            ),
            'div' => array
            (
                'icon'  => '',
                'label' => __( "div", 'molongui-authorship-pro' ),
            ),
            'p' => array
            (
                'icon'  => '',
                'label' => __( "p", 'molongui-authorship-pro' ),
            ),
        ),
    );

    return $html_tag;
}
add_filter( '_authorship/options/author_name_tag', 'authorship_pro_title_tag_markup' );
function authorship_pro_headline_tag_markup()
{
    $html_tag = array();
    $html_tag[] = array
    (
        'display'  => false,
        'advanced' => true,
        'deps'     => 'author_box',
        'search'   => '',
        'type'     => 'inline-dropdown',
        'class'    => '',
        'default'  => 'h3',
        'id'       => 'box_headline_tag',
        'title'    => '',
        'desc'     => '',
        'help'     => sprintf( __( "%sSelecting a value inline with the structure of your page might improve SEO.%s", 'molongui-authorship-pro' ), '<p>', '</p>' ),
        'label'    => sprintf( __( "Use %s tag for the %sheadline%s shown above the author box", 'molongui-authorship-pro' ),  '{input}', '<strong>', '</strong>' ),
        'options'  => array
        (
            'h1' => array
            (
                'icon'  => '',
                'label' => __( "h1", 'molongui-authorship-pro' ),
            ),
            'h2' => array
            (
                'icon'  => '',
                'label' => __( "h2", 'molongui-authorship-pro' ),
            ),
            'h3' => array
            (
                'icon'  => '',
                'label' => __( "h3", 'molongui-authorship-pro' ),
            ),
            'h4' => array
            (
                'icon'  => '',
                'label' => __( "h4", 'molongui-authorship-pro' ),
            ),
            'h5' => array
            (
                'icon'  => '',
                'label' => __( "h5", 'molongui-authorship-pro' ),
            ),
            'h6' => array
            (
                'icon'  => '',
                'label' => __( "h6", 'molongui-authorship-pro' ),
            ),
            'div' => array
            (
                'icon'  => '',
                'label' => __( "div", 'molongui-authorship-pro' ),
            ),
            'p' => array
            (
                'icon'  => '',
                'label' => __( "p", 'molongui-authorship-pro' ),
            ),
        ),
    );

    return $html_tag;
}
add_filter( '_authorship/options/headline_tag', 'authorship_pro_headline_tag_markup' );
if ( !function_exists( 'authorship_pro_get_authorship_tools_markup' ) )
{
    function authorship_pro_get_authorship_tools_markup()
    {
        $authorship_tools = array();
        $authorship_tools[] = array
        (
            'display' => true,
            'deps'    => '',
            'search'  => '',
            'type'    => 'button',
            'class'   => 'is-compact',
            'label'   => __( "Export posts authorship to have a backup", 'molongui-authorship-pro' ),
            'button'  => array
            (
                'display'  => true,
                'type'     => 'download',
                'id'       => 'export_authorship',
                'label'    => __( "Export", 'molongui-authorship-pro' ),
                'title'    => __( "Export posts authorship", 'molongui-authorship-pro' ),
                'class'    => 'm-export-authorship same-width',
                'disabled' => false,
            ),
        );
        $authorship_tools[] = array
        (
            'display' => true,
            'deps'    => '',
            'search'  => '',
            'type'    => 'button',
            'class'   => 'is-compact',
            'label'   => __( "Import posts authorship from a previous exported backup file", 'molongui-authorship-pro' ),
            'button'  => array
            (
                'display'  => true,
                'type'     => 'input',
                'id'       => 'import_authorship',
                'label'    => __( "Import", 'molongui-authorship-pro' ),
                'title'    => __( "Import posts authorship", 'molongui-authorship-pro' ),
                'class'    => 'm-import-authorship same-width',
                'disabled' => false,
                'multi'    => false,
                'accept'   => '.json', // Could be multiple extensions: 'image/png, image/jpeg'
            ),
        );
        $authorship_tools[] = array
        (
            'display' => true,
            'deps'    => '',
            'search'  => '',
            'type'    => 'button',
            'class'   => 'is-compact',
            'label'   => __( "Remove all posts authorship", 'molongui-authorship-pro' ),
            'button'  => array
            (
                'display'  => true,
                'type'     => 'action',
                'id'       => 'delete_authorship',
                'label'    => __( "Delete", 'molongui-authorship-pro' ),
                'title'    => __( "Delete posts authorship", 'molongui-authorship-pro' ),
                'class'    => 'm-delete-authorship same-width',
                'disabled' => false,
            ),
        );

        return $authorship_tools;
    }
    add_filter( '_authorship/options/authorship/tools/markup', 'authorship_pro_get_authorship_tools_markup' );
}
if ( !function_exists( 'authorship_pro_get_guest_tools_markup' ) )
{
    function authorship_pro_get_guest_tools_markup()
    {
        $guest_tools = array();
        $guest_tools[] = array
        (
            'display' => true,
            'deps'    => 'guest_authors',
            'search'  => '',
            'type'    => 'button',
            'class'   => 'is-compact',
            'label'   => __( "Export Guest Authors to have a backup or import them on another installation", 'molongui-authorship-pro' ),
            'button'  => array
            (
                'display'  => true,
                'type'     => 'download',
                'id'       => 'export_guests',
                'label'    => __( "Export", 'molongui-authorship-pro' ),
                'title'    => __( "Export Guest Authors", 'molongui-authorship-pro' ),
                'class'    => 'm-export-guests same-width',
                'disabled' => false,
            ),
        );
        $guest_tools[] = array
        (
            'display' => true,
            'deps'    => 'guest_authors',
            'search'  => '',
            'type'    => 'button',
            'class'   => 'is-compact',
            'label'   => __( "Import Guest Authors from a previous exported backup file", 'molongui-authorship-pro' ),
            'button'  => array
            (
                'display'  => true,
                'type'     => 'input',
                'id'       => 'import_guests',
                'label'    => __( "Import", 'molongui-authorship-pro' ),
                'title'    => __( "Import Guest Authors", 'molongui-authorship-pro' ),
                'class'    => 'm-import-guests same-width',
                'disabled' => false,
                'multi'    => false,
                'accept'   => '.json', // Could be multiple extensions: 'image/png, image/jpeg'

            ),
        );
        $guest_tools[] = array
        (
            'display' => true,
            'deps'    => 'guest_authors',
            'search'  => '',
            'type'    => 'button',
            'class'   => 'is-compact',
            'label'   => __( "Remove all Guest Authors", 'molongui-authorship-pro' ),
            'button'  => array
            (
                'display'  => true,
                'type'     => 'action',
                'id'       => 'delete_guests',
                'label'    => __( "Delete", 'molongui-authorship-pro' ),
                'title'    => __( "Delete Guest Authors", 'molongui-authorship-pro' ),
                'class'    => 'm-delete-guests same-width',
                'disabled' => false,
            ),
        );

        return $guest_tools;
    }
    add_filter( '_authorship/options/guest/tools/markup', 'authorship_pro_get_guest_tools_markup' );
}
function authorship_pro_options_help( $help, $option )
{
    switch( $option )
    {
        case 'box_post_types_auto':
            $help = sprintf( __( "%sPost types in which to automatically insert the author box. Leave it blank and use the setting below if you want to manually select on which items to add the author box.%s %sAuthor Boxes are usually displayed on blog posts. However, you may want to show them on a custom post type (like %sproducts%s, %scourses%s, %sprojects%s or %sleads%s).%s %s%sAuthor and post configuration might override this setting%s.%s ", 'molongui-authorship' ), '<p>', '</p>', '<p>', '<code>', '</code>', '<code>', '</code>', '<code>', '</code>', '<code>', '</code>', '</p>', '<p>', '<strong>', '</strong>', '</p>' );
        break;

        case 'box_post_types_manual':
            $help = sprintf( __( "%sPost types for which you want to have the option to configure —on a post level— whether to display the author box or not. Configuration is made from the edit screen.%s %sLeave it blank and use the setting above if you want the author box to be shown automatically on selected post types.%s", 'molongui-authorship' ), '<p>', '</p>', '<p>', '</p>' );
        break;

        case 'hide_if_no_bio':
            $help = sprintf( __( "%sWhen enabled, the author box will not display for authors without a description. %sUnless author is configured otherwise%s.%s", 'molongui-authorship' ), '<p>', '<strong>', '</strong>', '</p>' );
        break;
    }

    return $help;
}
add_filter( 'authorship/options/help', 'authorship_pro_options_help', 10, 2 );
add_filter( '_authorship/options/display_advanced_button', '__return_true' );