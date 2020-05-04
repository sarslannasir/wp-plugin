<?php

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if( ! class_exists( 'Product_Selector_Post_Type' ) ) :
	
class Product_Selector_Post_Type {
	
	/**
	 * Constructor
	 */
	function __construct() {
		add_action( 'init', array( $this, 'product_selector_register_post_types' ), 0 );
	}
	
	/**
	 * Register a team post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function product_selector_register_post_types() {
		if ( post_type_exists( "selectors" ) || post_type_exists( "ps-product" ) )
			return;
		
		// Questions
		$args = array(
			'labels'             => array(
				'name'               => _x( 'WP Product Selector', 'post type general name', 'wp-product-selector' ),
				'singular_name'      => _x( 'WP Product Selector', 'post type singular name', 'wp-product-selector' ),
				'menu_name'          => _x( 'WP Product Selector', 'admin menu', 'wp-product-selector' ),
				'name_admin_bar'     => _x( 'Selector', 'add new on admin bar', 'wp-product-selector' ),
				'add_new'            => _x( 'Add New', 'add new title', 'wp-product-selector' ),
				'add_new_item'       => __( 'Add a Question', 'wp-product-selector' ),
				'new_item'           => __( 'New WP Product Selector', 'wp-product-selector' ),
				'edit_item'          => __( 'Edit Question', 'wp-product-selector' ),
				'view_item'          => __( 'View WP Product Selector', 'wp-product-selector' ),
				'all_items'          => __( 'All Questions', 'wp-product-selector' ),
				'search_items'       => __( 'Search WP Product Selector', 'wp-product-selector' ),
				'parent_item_colon'  => __( 'Parent WP Product Selector:', 'wp-product-selector' ),
				'not_found'          => __( 'No teams found.', 'wp-product-selector' ),
				'not_found_in_trash' => __( 'No teams found in Trash.', 'wp-product-selector' )
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'questions' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' ),
			'menu_icon'			 => 'dashicons-cart'
		);

		register_post_type( 'ps-questions', $args );


		// Add new taxonomy, NOT hierarchical (like tags)
		$selector_args = array(
			'hierarchical'          => false,		// False to work like Tags
			'labels'                => array(
				'name'                       => _x( 'Selectors', 'taxonomy general name', 'wp-product-selector' ),
				'singular_name'              => _x( 'Selectors', 'taxonomy singular name', 'wp-product-selector' ),
				'search_items'               => __( 'Search Writers', 'wp-product-selector' ),
				'popular_items'              => __( 'Popular Writers', 'wp-product-selector' ),
				'all_items'                  => __( 'All Writers', 'wp-product-selector' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit Selectors', 'wp-product-selector' ),
				'update_item'                => __( 'Update Selectors', 'wp-product-selector' ),
				'add_new_item'               => __( 'Add New Selectors', 'wp-product-selector' ),
				'new_item_name'              => __( 'New Selectors Name', 'wp-product-selector' ),
				'separate_items_with_commas' => __( 'Separate selectors with commas', 'wp-product-selector' ),
				'add_or_remove_items'        => __( 'Add or remove selectors', 'wp-product-selector' ),
				'choose_from_most_used'      => __( 'Choose from the most used selectors', 'wp-product-selector' ),
				'not_found'                  => __( 'No selectors found.', 'wp-product-selector' ),
				'menu_name'                  => __( 'Selectors', 'wp-product-selector' ),
			),
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'selector-category' ),
		);

		register_taxonomy( 'selector-category', 'ps-questions', $selector_args );
	}
	
	function product_selector_rewrite_flush() {
		// First, we "add" the custom post type via the above written function.
		// Note: "add" is written with quotes, as CPTs don't get added to the DB,
		// They are only referenced in the post_type column with a post entry, 
		// when you add a post of this CPT.
		$this->product_selector_register_post_types();

		// ATTENTION: This is *only* done during plugin activation hook in this example!
		// You should *NEVER EVER* do this on every page load!!
		flush_rewrite_rules();
	}
	
}
	
endif;