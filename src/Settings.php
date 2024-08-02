<?php

namespace CranleighSchool\CranleighPeople;

if ( ! class_exists( 'CranleighSchool\CranleighPeople\Settings' ) ) {
	class Settings {

		public const SETTINGS_SECTION_ID = 'cran_people_basic';
        public const SETTINGS_SECTION_IMPORTER = 'cran_people_importer';
		private $settings_api;

		public static function register() {
			$instance = new self();
			$instance->settings_api = new WeDevsSettingsAPI();

			add_action( 'admin_init', array( $instance, 'admin_init' ) );
			add_action( 'admin_menu', array( $instance, 'admin_menu' ) );
		}

		public function admin_init() {

			// set the settings
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			// initialize settings
			$this->settings_api->admin_init();
		}

		public function admin_menu() {
			add_options_page( 'Cranleigh People', 'Cranleigh People', 'manage_options', 'cranleigh_people_settings', array( $this, 'plugin_page' ) );
		}

		public function get_settings_sections() {
			$sections = array(
				array(
					'id'    => self::SETTINGS_SECTION_ID,
					'title' => __( 'Cranleigh People', 'cranleigh-2016' ),
				),
                array(
                    'id'    => self::SETTINGS_SECTION_IMPORTER,
                    'title' => __( 'Importer Settings', 'cranleigh-2016' ),
                ),
			);

			return $sections;
		}

		/**
		 * Returns all the settings fields.
		 *
		 * @return array settings fields
		 */
		public function get_settings_fields() {
			$settings_fields = array(
				self::SETTINGS_SECTION_ID => array(
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
						'options' => array(
							'yes' => 'Yes, it is',
							'no'  => 'No it\'s not, please let me edit things like a normal Wordpress post',
						),
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
						'desc'    => "Which site did you want to grab data from?",
						'type'    => 'radio',
						'default' => 1,
						'options' => $this->selectSite_optionList(),
					),

                    array(
                        'name' => 'data_ingest_type',
                        'label' => 'How is data ingested?',
                        'desc' => 'Either People Manager pushes data to this site, or this site pulls data from the People Manager using a API key',
                        'type' => 'radio',
                        'options' => array(
                            'push' => 'Push data to this site',
                            'pull' => 'Pull data from the People Manager',
                        ),
                        'default' => 'push',
                    )
				),
                self::SETTINGS_SECTION_IMPORTER => array (
                    array(
                        'name' => 'ip_allowlist',
                        'label' => 'IP Address that the Push is coming from',
                        'desc' => 'A single IP address of the location that the push is coming from',
                        'type' => 'text'
                    ),
                    array(
                        'name' => 'importer_api_endpoint',
                        'label' => 'Importer API Endpoint (full url)',
                        'desc' => 'The full url of the People Manager endpoint',
                        'type' => 'url',
                    ),
                    array(
                        'name' => 'importer_api_key',
                        'label' => 'Importer API Key',
                        'desc' => 'The API Key for the People Manager',
                        'type' => 'text',
                        'default' => 'apikey',
                    ),
                    array(
                        'name' => 'disable_wp_cron',
                        'label' => 'Disable WP Cron',
                        'desc' => 'Disable WP Cron and import things manually when you want to?',
                        'type'    => 'radio',
                        'options' => array(
                            'yes' => 'Yes, disable WP Cron',
                            'no'  => 'No, leave it alone',
                        ),
                        'default' => 'no',
                    ),
                    array(
                        'name'    => 'slack_webhook_endpoint',
                        'label'   => 'Slack Webhook Endpoint',
                        'desc'    => 'The Slack Webhook, for sending notifications on importing',
                        'type'    => 'text',
                        'default' => null,
                    ),
                )
			);

			return $settings_fields;
		}

		public function get_sites() {
			if ( is_multisite() === false ) {
				return new \WP_Error( 'Not A MultiSite', "This is not a multi site therefore I can't let you call a function that won't exist!" );
			}

			$subsites = get_sites();
			$output = array();
			foreach ( $subsites as $subsite ) {
				$subsite_id = get_object_vars( $subsite )['blog_id'];
				$subsite_name = get_blog_details( $subsite_id )->blogname;
				$subsite->name = $subsite_name;
				$output[] = $subsite;
			}

			return $output;
		}

		public function selectSite_optionList() {
			$sites = $this->get_sites();
			if ( is_wp_error( $sites ) ) {
				return array( 1 => 'This is not a multisite - so you have no choice here!' );
			}

			$list = array();
			foreach ( $this->get_sites() as $site ) {
				$list[ $site->blog_id ] = $site->name;
			}

			return $list;
		}

		public function plugin_page() {
			echo '<div class="wrap">';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';
		}

		/**
		 * Get all the pages.
		 *
		 * @return array page names with key value pairs
		 */
		public function get_pages() {
			$pages = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}
	}
}
