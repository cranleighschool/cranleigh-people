<?php

namespace CranleighSchool\CranleighPeople;

class CustomPostType {

	public const POST_TYPE_KEY = 'staff';

	public static function register() {
		$instance = new self();
		add_action( 'init', array( $instance, 'CPT_Cranleigh_People' ) );
	}

	public function CPT_Cranleigh_People() {
		$public = $this->loading_cpt();

		$labels = array(
			'name'                  => _x( 'Cranleigh People', 'Post Type General Name', 'cranleigh-2016' ),
			'singular_name'         => _x( 'Person', 'Post Type Singular Name', 'cranleigh-2016' ),
			'menu_name'             => __( 'People', 'cranleigh-2016' ),
			'name_admin_bar'        => __( 'People', 'cranleigh-2016' ),
			'archives'              => __( 'All Staff', 'cranleigh-2016' ),
			'parent_item_colon'     => __( 'Parent Item:', 'cranleigh-2016' ),
			'all_items'             => __( 'All Cranleigh People', 'cranleigh-2016' ),
			'add_new_item'          => __( 'Add New Person', 'cranleigh-2016' ),
			'add_new'               => __( 'Add New', 'cranleigh-2016' ),
			'new_item'              => __( 'New Person', 'cranleigh-2016' ),
			'edit_item'             => __( 'Edit Person', 'cranleigh-2016' ),
			'update_item'           => __( 'Update Person', 'cranleigh-2016' ),
			'view_item'             => __( 'View Person', 'cranleigh-2016' ),
			'search_items'          => __( 'Search Person', 'cranleigh-2016' ),
			'not_found'             => __( 'Not found', 'cranleigh-2016' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'cranleigh-2016' ),
			'featured_image'        => __( 'Profile Picture', 'cranleigh-2016' ),
			'set_featured_image'    => __( 'Select Profile Picture', 'cranleigh-2016' ),
			'remove_featured_image' => __( 'Remove Profile Picture', 'cranleigh-2016' ),
			'use_featured_image'    => __( 'Use as Profile Picture', 'cranleigh-2016' ),
			'insert_into_item'      => __( 'Insert into item', 'cranleigh-2016' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'cranleigh-2016' ),
			'items_list'            => __( 'People list', 'cranleigh-2016' ),
			'items_list_navigation' => __( 'People list navigation', 'cranleigh-2016' ),
			'filter_items_list'     => __( 'Filter people list', 'cranleigh-2016' ),
		);
		$capabilities = array(
			'publish_posts'       => 'publish_' . self::POST_TYPE_KEY,
			'edit_posts'          => 'edit_' . self::POST_TYPE_KEY,
			'edit_others_posts'   => 'edit_others_' . self::POST_TYPE_KEY,
			'delete_posts'        => 'delete_' . self::POST_TYPE_KEY,
			'delete_others_posts' => 'delete_others_' . self::POST_TYPE_KEY,
			'read_private_posts'  => 'read_private_' . self::POST_TYPE_KEY,
			'edit_post'           => 'edit_' . self::POST_TYPE_KEY,
			'delete_post'         => 'delete_' . self::POST_TYPE_KEY,
			'read_post'           => 'read_' . self::POST_TYPE_KEY,
		);
		$args = array(
			'label'                 => __( 'Person', 'cranleigh-2016' ),
			'description'           => __( 'A List of People that are mentioned on the website', 'cranleigh-2016' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => $public,
			'show_ui'               => $public,
			'show_in_menu'          => $public,
			'menu_position'         => 27,
			'menu_icon'             => 'dashicons-businessman',
			'show_in_admin_bar'     => $public,
			'show_in_nav_menus'     => $public,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capabilities'          => $capabilities,
			'show_in_rest'          => $public,
			'rest_base'             => 'people',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);
		register_post_type( self::POST_TYPE_KEY, $args );
		$this->roles_and_caps();
	}

	// Register Custom Taxonomy

	/**
	 * @return bool
	 */
	private function loading_cpt(): bool {
		if ( Plugin::getPluginSetting( 'load_cpt' ) == 'yes' ) {
			return true;
		} else {
			return false;
		}
	}

	private function roles_and_caps() {
		$admin_role = get_role( 'administrator' );
		$editor_role = get_role( 'editor' );
		$caps = array(
			'publish_' . self::POST_TYPE_KEY,
			'edit_' . self::POST_TYPE_KEY,
			'edit_others_' . self::POST_TYPE_KEY,
			'delete_' . self::POST_TYPE_KEY,
			'delete_others_' . self::POST_TYPE_KEY,
			'read_private_' . self::POST_TYPE_KEY,
			'edit_' . self::POST_TYPE_KEY,
			'read_' . self::POST_TYPE_KEY,
			'manage_staff_cats',
			'edit_staff_cats',
			'delete_staff_cats',
			'assign_staff_cats',
		);

		foreach ( $caps as $cap ) {
			$editor_role->add_cap( $cap );
			$admin_role->add_cap( $cap );
		}
	}
}
