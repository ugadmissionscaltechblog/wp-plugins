<?php

/**
 * Plugin Name: Elementor Author Gallery Widget
 * Description: Elementor Author Gallery Widget
 * Version:     1.0.0
 * Author:      Michael Guutz
 * Author URI:  https://developers.elementor.com/
 * Text Domain: elementor-addon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function register_author_gallery_widget( $widgets_manager ) {
    // require_once( __DIR__ . '/widgets/author-gallery.php' );
    require_once( __DIR__ . '/widgets/blogger-box.php' );
	// $widgets_manager->register( new \AuthorGallery() );
    $widgets_manager->register( new \Blogger_Box() );
}
add_action( 'elementor/widgets/register', 'register_author_gallery_widget' );

function register_widget_styles() {
	wp_register_style( 'caltech-blogger-box', plugins_url( 'assets/blogger-box.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'register_widget_styles' );


function shortcode_link($atts, $content = null){    
	$default = array(
        'link' => '#',
    );
    $a = shortcode_atts($default, $atts);
    $content = do_shortcode($content);
    return '<a href="'.($a['link']).'" style="color: #ff6c0c">'.$content.'</a>';
}

add_shortcode('guutz_link_hack', 'shortcode_link');

use Molongui\Authorship\Author;
/**
 * Format author details for given list of author objects or IDs.
 *
 * @param array $authors_list An array of author objects or IDs.
 * @return array An array of authors with their formatted details.
 */
function format_author_details($authors_list) {
    $authors = [];

    foreach ($authors_list as $author_obj) {
        $id = $author_obj->id;
        if (property_exists($author_obj, 'type')) {
            $type = $author_obj->type;
        } else {
            $type = 'user';
        }
        $user = new Author($id, $type, null, false);

        if ($user) {
            add_filter( 'authorship/pre_author_link', 'authorship_dont_filter_author_link', 10, 4 );
            add_filter( 'molongui_authorship_dont_filter_name', '__return_true' );
            $authors[] = [
                'avatar' => $user->get_avatar('thumbnail', 'url'),
                'display_name' => $user->get_name(),
                'website' => $user->get_url(),
                'bio' => $user->get_bio(),
                'posts_url' => $user->get_url()
            ];
            error_log('User found for author ID: ' . $id . ' and type: ' . $type . ' with display name: ' . $user->get_name());
            remove_filter( 'molongui_authorship_dont_filter_name', '__return_true' );
            remove_filter( 'authorship/pre_author_link', 'authorship_dont_filter_author_link', 10 );
        } else {
            error_log('User not found for author ID: ' . $id . ' and type: ' . $type);
        }
    }

    return $authors;
}

function get_current_bloggers() {
    $query = new WP_User_Query(array(
        'role' => 'editor',
        'fields' => array('ID')
    ));
    $bloggers = $query->get_results();
    error_log('result: ' . print_r($bloggers, true));
    return $bloggers;
}

function get_guest_bloggers() {
    $query = new WP_User_Query(array(
        'role' => 'author',
        'fields' => array('ID')
    ));
    $bloggers = $query->get_results();
    error_log('result: ' . print_r($bloggers, true));
    return $bloggers;
}

function get_retired_bloggers() {
    $query = new WP_User_Query(array(
        'role' => 'contributor',
        'fields' => array('ID')
    ));
    $bloggers = $query->get_results();
    error_log('result: ' . print_r($bloggers, true));
    return $bloggers;
}