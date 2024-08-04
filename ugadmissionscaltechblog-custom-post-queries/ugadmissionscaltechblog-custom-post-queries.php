<?php

/**
 * Plugin Name: ugadmissionscaltechblog-custom-post-queries
 * Description: for use in elementor custom query field
 * Version:     1.0.0
 * Author:      Michael Guutz
 * Author URI:  https://developers.elementor.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Molongui\Authorship\Author;

add_action( 'elementor/query/currentbloggersquery', function( $query ) {
    global $wpdb;
	
    $final_query = "
        SELECT p.ID
        FROM {$wpdb->posts} AS p
        INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id AND pm.meta_key = '_molongui_author'
        WHERE p.post_type = 'post' AND p.post_status = 'publish'
        AND pm.meta_value LIKE 'user%'
        GROUP BY p.post_author
    ";

    $post_ids = $wpdb->get_col( $final_query );

    if ( ! empty( $post_ids ) ) {
        $query->set( 'post__in', $post_ids );
        $query->set( 'orderby', 'date' );
        $query->set( 'posts_per_page', count( $post_ids ) );
    } else {
        $query->set( 'post__in', array( 0 ) ); // No posts should match
    }
} );

add_action( 'elementor/query/retiredbloggersquery', function( $query ) {
	global $wpdb;
	
	$final_query = "SELECT MAX(p.ID) AS post_id
		FROM {$wpdb->posts} AS p
		INNER JOIN {$wpdb->postmeta} AS pm ON pm.meta_key = '_molongui_author' AND pm.meta_value LIKE 'guest%'
		AND p.ID = pm.post_id AND p.post_status = 'publish'
		WHERE DATE(p.post_date) < '2023-07-01'
		GROUP BY pm.meta_value;
	";

	$post_ids = $wpdb->get_col( $final_query );

    if ( ! empty( $post_ids ) ) {
		$query->init();
        $query->set( 'post__in', $post_ids );
        $query->set( 'orderby', 'date' );
        $query->set( 'posts_per_page', count( $post_ids ) );
    } else {
        $query->set( 'post__in', array(0) );
    }
} );

if ( !function_exists( 'get_spotlight_posts' ) )
{
    function get_spotlight_posts() {
        global $wpdb;
	
        $final_query = "WITH RankedPosts AS (
            SELECT
                p.ID,
                p.post_title AS name,
                p.post_date,
                t.slug,
                ROW_NUMBER() OVER (PARTITION BY COALESCE(tt.term_taxonomy_id, p.ID) ORDER BY p.post_date DESC) AS rank
            FROM {$wpdb->posts} AS p
            LEFT JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
            LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
            WHERE p.post_type = 'post'
            AND p.post_status = 'publish'
            AND (tt.taxonomy IN ('category', 'post_tag') OR tt.term_taxonomy_id IS NULL)
            AND (t.slug IN ('research', 'academics', 'student-life', 'local', 'global', 'before-college', 'after-caltech', 'how-to-caltech', 'announcements', 'spotlight') OR t.slug IS NULL)
        ),
        FilteredPosts AS (
            SELECT ID, name, post_date, slug
            FROM RankedPosts
            WHERE rank = 1
        ),
        SpotlightPost AS (
            SELECT ID, name, post_date, slug
            FROM FilteredPosts
            WHERE slug = 'spotlight'
            ORDER BY post_date DESC
            LIMIT 1
        )
        SELECT *
        FROM FilteredPosts
        WHERE ID NOT IN (SELECT ID FROM SpotlightPost)
        UNION ALL
        SELECT *
        FROM SpotlightPost;
        ";
    
        $post_ids = $wpdb->get_col( $final_query );
        return $post_ids;
    }
}


add_action('ultimate_post_kit_pro/query/homepagecarouselquery', function($query) {

    $post_ids = get_spotlight_posts();
	
    if ( ! empty( $post_ids ) ) {
		$query->init();
        $query->set( 'post__in', $post_ids );
        $query->set( 'orderby', 'random' );
        // $query->set( 'posts_per_page', count( $post_ids ) );
    } else {
        $query->set( 'post__in', array(0) );
    }
});


add_action('ultimate_post_kit_pro/query/newpostsquery', function($query) {
    
    if ( ! empty( $post_ids ) ) {
        $query->init();
        $query->set( 'orderby', 'date' );
        $query->set( 'posts_per_page', 6 );
    }
});

add_action('ultimate_post_kit_pro/query/nextpostsquery', function($query) {

    $post_ids = get_spotlight_posts();
    
    if ( ! empty( $post_ids ) ) {
        $query->init();
        $query->set( 'post__not_in', $post_ids );
        $query->set( 'orderby', 'date' );
        $query->set( 'offset', 6 );
        $query->set( 'posts_per_page', 12 );
    }
});

if ( !function_exists( 'get_molongui_post_avatar' ) )
{
    function get_molongui_post_avatar( $post_id )
	{
		$post_authors = get_post_authors( $post_id );
		if ( empty( $post_authors ) or $post_authors[0]->id == 0 ) return 0;
		$author = new Author( $post_authors[0]->id, $post_authors[0]->type );
		return $author->get_avatar(array(48,48));
	}
}

function prefix_console_log_message( $message ) {

    $message = htmlspecialchars( stripslashes( $message ) );
    //Replacing Quotes, so that it does not mess up the script
    $message = str_replace( '"', "-", $message );
    $message = str_replace( "'", "-", $message );

    return "<script>console.log('{$message}')</script>";
}

function display_author_info() {
    global $post;
    $author_id = $post->post_author;
    $author_name = get_the_author_meta('display_name', $author_id);
    $author_bio = get_the_author_meta('description', $author_id);
    
    return '<div class="author-info">
                <h3>' . esc_html($author_name) . '</h3>
                <p>' . esc_html($author_bio) . '</p>
            </div>';
}
add_shortcode('author_info', 'display_author_info');