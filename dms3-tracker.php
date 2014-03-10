<?php
/**
 * Plugin Name: DeMomentSomTres Tracker
 * Plugin URI: http://DeMomentSomTres.com
 * Description: Implementa un tracker usant WordPress
 * Version: 1.1
 * Author: Marc Queralt
 * Author URI: http://DeMomentSomTres.com
 */

/** 
 * Register Custom Post Type
 * @since 1.0 
 */
function dms3_tracking_posttype() {

	$labels = array(
		'name'                => _x( 'Usage registers', 'Post Type General Name', 'dms3-tracker' ),
		'singular_name'       => _x( 'Usage register', 'Post Type Singular Name', 'dms3-tracker' ),
		'menu_name'           => __( 'Tracking', 'dms3-tracker' ),
		'parent_item_colon'   => __( 'Parent Item:', 'dms3-tracker' ),
		'all_items'           => __( 'Usage registers', 'dms3-tracker' ),
		'view_item'           => __( 'View Item', 'dms3-tracker' ),
		'add_new_item'        => __( 'Add New Item', 'dms3-tracker' ),
		'add_new'             => __( 'Add New', 'dms3-tracker' ),
		'edit_item'           => __( 'Edit Item', 'dms3-tracker' ),
		'update_item'         => __( 'Update Item', 'dms3-tracker' ),
		'search_items'        => __( 'Search Item', 'dms3-tracker' ),
		'not_found'           => __( 'Not found', 'dms3-tracker' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'dms3-tracker' ),
	);
	$args = array(
		'label'               => __( 'dms3_tracking', 'dms3-tracker' ),
		'description'         => __( 'Usage tracking registers', 'dms3-tracker' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-welcome-learn-more',
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
	);
	register_post_type( 'dms3_tracking', $args );

}

/**
 * @since 1.1
 */
function dms3_tracking_add_metaboxes() {
        add_meta_box(
            'dms3_tracking',
            __( 'Pretty Print', 'dms3_tracker' ),
            'dms3_tracking_custom_box',
            'dms3_tracking'
        );
}

/**
 * @since 1.1
 */
function dms3_tracking_custom_box($post) {
    echo '<pre>';
    print_r(json_decode($post->post_content));
    echo '</pre>';
}
        
// Hook into the 'init' action
add_action( 'init', 'dms3_tracking_posttype', 0 );
add_action( 'add_meta_boxes', 'dms3_tracking_add_metaboxes' ); //+1.1
?>
