<?php

namespace CranleighSchool\CranleighPeople;

if ( ! class_exists( 'CranleighSchool\CranleighPeople\Settings' ) ) :


	class Settings {

		private $settings_api;

		function __construct() {
			$this->settings_api = new WeDevsSettingsAPI();

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		function admin_init() {

			// set the settings
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			// initialize settings
			$this->settings_api->admin_init();
		}

		function admin_menu() {
			add_options_page( 'Cranleigh People', 'Cranleigh People', 'manage_options', 'cranleigh_people_settings', array( $this, 'plugin_page' ) );
		}

		function get_settings_sections() {
			$sections = array(
				array(
					'id'    => 'cran_people_basic',
					'title' => __( 'Cranleigh People', 'cranleigh-2016' ),
				),

			);
			return $sections;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		function get_settings_fields() {
			$settings_fields = array(
				'cran_people_basic' => array(
					array(
						'name'    => 'load_cpt',
						'label'   => __( 'Load Custom Post Type', 'cranleigh-2016' ),
						'desc'    => __( 'Do you want to load the custom post type, or just the widgets and shortcodes?', 'cranleigh-2016' ),
						'type'    => 'radio',
						'options' => array(
							'yes' => 'Yes, load everything.',
							'no'  => 'No, I only need the widgets and shortcodes.',
						),
						'default' => 'no',
					),
					array(
						'name'    => 'isams_controlled',
						'label'   => __( 'Under Isams Control', 'cranleigh-2016' ),
						'desc'    => __( 'If this instance under iSAMS control?', 'cranleigh-2016' ),
						'type'    => 'radio',
						'options' => [
							'yes' => 'Yes, it is',
							'no'  => 'No it\'s not, please let me edit things like a normal Wordpress post',
						],
					),

					array(
						'name'              => 'default_photo_attachment_id',
						'label'             => __( 'Default Photo Attachment ID', 'cranleigh-2016' ),
						'desc'              => 'The attachment ID of the photo you want to use for the default photo',
						'type'              => 'text',
						'sanitize_callback' => 'intval',
						'default'           => 32492,
					),

					array(
						'name'    => 'load_from_blog_id',
						'label'   => 'Which Blog ID to load from',
						'desc'    => "Which site did you want to grab data from? (eg. for Houses you will mostly put &quot;Cranleigh School&quot; but for Cranleigh Abu Dhabi Site, you'll need to put their site!)",
						'type'    => 'radio',
						'default' => 1,
						'options' => $this->selectSite_optionList(),
					),

				),
			);

			return $settings_fields;
		}

		function get_sites() {
			if ( is_multisite() === false ) {
				return new \WP_Error( 'Not A MultiSite', "This is not a multi site therefore I can't let you call a function that won't exist!" );
			}

			$subsites = get_sites();
			$output   = array();
			foreach ( $subsites as $subsite ) :
				$subsite_id    = get_object_vars( $subsite )['blog_id'];
				$subsite_name  = get_blog_details( $subsite_id )->blogname;
				$subsite->name = $subsite_name;
				$output[]      = $subsite;
			endforeach;
			return $output;
		}

		function selectSite_optionList() {

			$sites = $this->get_sites();
			if ( is_wp_error( $sites ) ) {
				return [ 1 => 'This is not a multisite - so you have no choice here!' ];
			}

			$list = array();
			foreach ( $this->get_sites() as $site ) :
				$list[ $site->blog_id ] = $site->name;
			endforeach;

			return $list;
		}

		function plugin_page() {
			echo '<div class="wrap">';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		function get_pages() {
			$pages         = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}

	}
	endif;


