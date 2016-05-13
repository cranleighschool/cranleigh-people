<?php
/*
	Plugin Name: Cranleigh People
	Plugin URI: http://www.cranleigh.org
	Description: One plugin that controls the people who work at Cranleigh. 
	Author: Fred Bradley
	Version: 1
	Author URI: http://fred.im
*/
require_once(dirname(__FILE__).'/settingsapiwrapper.php');
require_once(dirname(__FILE__).'/settings.php');

class cran_peeps {
	public $post_type_key = "staff";
	function __construct() {
		
		add_action( 'init', array($this, 'CPT_Cranleigh_People'));
		add_filter( 'rwmb_meta_boxes', array($this, 'meta_boxes'));
		add_action( 'init', array($this, 'TAX_staff_cats'));

		add_action( 'admin_menu' , array($this, 'remove_staff_cats_tax_box'));
		
	}
	function remove_staff_cats_tax_box() {
		remove_meta_box( 'staff_categoriesdiv' , 'staff' , 'side' );
	}
	//Meta Boxes
	function meta_boxes($meta_boxes) {
		$prefix = "staff_";
		$meta_boxes[] = array(
			"id" => "staff_info",
			"title" => "Staff Info",
			"post_types" => array($this->post_type_key),
			"context" => "side",
			"priority" => "high",
			"autosave" => true,
			"fields" => array(
				array(
					"name" => __("Position(s)", "text_domain"),
					"id" => "{$prefix}position",
					"type" => "autocomplete",
					"clone" => true,
					'options'     => $this->staff_roles(),
					"desc" => "Start typing a role. If the role you're after doesn't exist then add it <a href='edit-tags.php?taxonomy=staff_categories&post_type=staff' target='_blank'>here</a>. If you have more than one role, add them all here."
				),
			
			),
			'validation' => array(
				'rules'    => array(
					"{$prefix}position" => array(
						'required'  => true,
						'minlength' => 3,
					),
				),
				// optional override of default jquery.validate messages
				'messages' => array(
					"{$prefix}position" => array(
						'required'  => __( 'Position is required', 'text_domain' ),
						'minlength' => __( 'Position must be at least 3 characters', 'text_domain' ),
					),
				),
			),
		);
		return $meta_boxes;
	}
	
	// Register Custom Post Type
	function CPT_Cranleigh_People() {
	
		$labels = array(
			'name'                  => _x( 'People', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Person', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'People', 'text_domain' ),
			'name_admin_bar'        => __( 'People', 'text_domain' ),
			'archives'              => __( 'Cranleigh People', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
			'all_items'             => __( 'All Cranleigh People', 'text_domain' ),
			'add_new_item'          => __( 'Add New Person', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'new_item'              => __( 'New Person', 'text_domain' ),
			'edit_item'             => __( 'Edit Person', 'text_domain' ),
			'update_item'           => __( 'Update Person', 'text_domain' ),
			'view_item'             => __( 'View Person', 'text_domain' ),
			'search_items'          => __( 'Search Person', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Profile Picture', 'text_domain' ),
			'set_featured_image'    => __( 'Select Profile Picture', 'text_domain' ),
			'remove_featured_image' => __( 'Remove Profile Picture', 'text_domain' ),
			'use_featured_image'    => __( 'Use as Profile Picture', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
			'items_list'            => __( 'People list', 'text_domain' ),
			'items_list_navigation' => __( 'People list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter people list', 'text_domain' ),
		);
		$args = array(
			'label'                 => __( 'Person', 'text_domain' ),
			'description'           => __( 'A List of People that are mentioned on the website', 'text_domain' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', ),
			'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-businessman',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,		
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( $this->post_type_key, $args );
	
	}
	
	// Register Custom Taxonomy
	function TAX_staff_cats() {
	
		$labels = array(
			'name'                       => _x( 'Staff Groups', 'Taxonomy General Name', 'text_domain' ),
			'singular_name'              => _x( 'Staff Group', 'Taxonomy Singular Name', 'text_domain' ),
			'menu_name'                  => __( 'Staff Groups', 'text_domain' ),
			'all_items'                  => __( 'All Staff Groups', 'text_domain' ),
			'parent_item'                => __( 'Parent Group', 'text_domain' ),
			'parent_item_colon'          => __( 'Parent Group:', 'text_domain' ),
			'new_item_name'              => __( 'New Group Name', 'text_domain' ),
			'add_new_item'               => __( 'Add New Group', 'text_domain' ),
			'edit_item'                  => __( 'Edit Group', 'text_domain' ),
			'update_item'                => __( 'Update Group', 'text_domain' ),
			'view_item'                  => __( 'View Group', 'text_domain' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
			'add_or_remove_items'        => __( 'Add or remove groups', 'text_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
			'popular_items'              => __( 'Popular Groups', 'text_domain' ),
			'search_items'               => __( 'Search Groups', 'text_domain' ),
			'not_found'                  => __( 'Not Found', 'text_domain' ),
			'no_terms'                   => __( 'No items', 'text_domain' ),
			'items_list'                 => __( 'Items list', 'text_domain' ),
			'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
		);
		register_taxonomy( 'staff_categories', array( 'staff' ), $args );
	
	}
	
	function staff_roles() {
		$args = array(
			"hide_empty" => false
		);
		$terms = get_terms("staff_categories", $args);
		$output = array();
		foreach($terms as $role) {
			$output[$role->slug] = $role->name;
		}
		return $output;
	}

}

$cran_peeps_plugin = new cran_peeps();

