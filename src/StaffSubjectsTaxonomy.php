<?php

namespace CranleighSchool\CranleighPeople;

class StaffSubjectsTaxonomy {

	public const TAXONOMY_KEY = 'staff_subjects';

	public static function register() {
		$instance = new self();
		add_action( 'init', array( $instance, 'init' ) );
	}

	public function init() {
		$labels = array(
			'name'                       => _x( 'Subjects', 'Taxonomy General Name', 'cranleigh-2016' ),
			'singular_name'              => _x( 'Subject', 'Taxonomy Singular Name', 'cranleigh-2016' ),
			'menu_name'                  => __( 'Subjects', 'cranleigh-2016' ),
			'all_items'                  => __( 'All Subjects', 'cranleigh-2016' ),
			'parent_item'                => __( 'Parent Group', 'cranleigh-2016' ),
			'parent_item_colon'          => __( 'Parent Group:', 'cranleigh-2016' ),
			'new_item_name'              => __( 'New Subject', 'cranleigh-2016' ),
			'add_new_item'               => __( 'Add New Subject', 'cranleigh-2016' ),
			'edit_item'                  => __( 'Edit Subject', 'cranleigh-2016' ),
			'update_item'                => __( 'Update Subject', 'cranleigh-2016' ),
			'view_item'                  => __( 'View Subject', 'cranleigh-2016' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'cranleigh-2016' ),
			'add_or_remove_items'        => __( 'Add or remove subjects', 'cranleigh-2016' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'cranleigh-2016' ),
			'popular_items'              => __( 'Popular Subjects', 'cranleigh-2016' ),
			'search_items'               => __( 'Search Subjects', 'cranleigh-2016' ),
			'not_found'                  => __( 'Not Found', 'cranleigh-2016' ),
			'no_terms'                   => __( 'No items', 'cranleigh-2016' ),
			'items_list'                 => __( 'Items list', 'cranleigh-2016' ),
			'items_list_navigation'      => __( 'Items list navigation', 'cranleigh-2016' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'capabilities'      => array(
				'manage_terms' => 'manage_staff_cats',
				'edit_terms'   => 'edit_staff_cats',
				'delete_terms' => 'delete_staff_cats',
				'assign_terms' => 'assign_staff_cats',
			),
		);
		register_taxonomy( self::TAXONOMY_KEY, array( CustomPostType::POST_TYPE_KEY ), $args );
	}
}
