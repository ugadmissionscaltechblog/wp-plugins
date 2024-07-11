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