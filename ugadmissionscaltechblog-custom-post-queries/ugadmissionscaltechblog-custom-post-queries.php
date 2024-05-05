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

use Molongui\Authorship\Includes\Author;

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
        $query->set( 'posts_per_page', -1 );
    } else {
        $query->set( 'post__in', array(0) );
    }
} );

add_action('ultimate_post_kit_pro/query/homepagecarouselquery', function($query) {
	global $wpdb;
	
	$final_query = "WITH RankedPosts AS (
    SELECT
        p.ID,
        p.post_title AS name,
		p.post_date,
        t.slug,
        ROW_NUMBER() OVER (PARTITION BY tt.term_taxonomy_id ORDER BY p.post_date DESC) AS rank
    FROM {$wpdb->terms} AS t
    LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
    LEFT JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
    LEFT JOIN {$wpdb->posts} AS p ON tr.object_id = p.ID
    WHERE tt.taxonomy IN ('category', 'post_tag')
    AND t.slug IN ('research', 'academics', 'student-life', 'local', 'global', 'before-college', 'after-caltech', 'how-to-caltech', 'announcements', 'spotlight')
    AND p.post_type = 'post'
    AND p.post_status = 'publish'
)
SELECT (SELECT DISTINCT ID
FROM RankedPosts
WHERE rank = 1 AND slug = 'spotlight') UNION
(SELECT DISTINCT ID
FROM RankedPosts
WHERE rank = 1 AND slug != 'spotlight');";

	$post_ids = $wpdb->get_col( $final_query );
	
    if ( ! empty( $post_ids ) ) {
		$query->init();
		$query->posts = $post_ids;
		$query->post_count = count($post_ids);
    } else {
        $query->set( 'post__in', array(0) );
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