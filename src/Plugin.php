<?php

	namespace CranleighSchool\CranleighPeople;

	use WP_Query;

	class Plugin extends BaseController
	{

		public CONST POST_TYPE_KEY = 'staff';
		public CONST PROFILE_PHOTO_SIZE_NAME = 'staff-photo';
		public $isams_controlled = false;

		private $load_cpt = false;

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct(string $plugin_name)
		{

			parent::__construct($plugin_name);

			$this->load();

			$this->loadShortcodes();


			if (isset($this->settings['isams_controlled']) && $this->settings['isams_controlled'] == 'yes') {
				$this->isams_controlled = true;
			} else {
				$this->isams_controlled = false;
			}

			if (isset($this->settings['load_cpt'])) :

				if ($this->settings['load_cpt'] == 'yes') :

					$this->load_cpt = true;

					$this->load_if_cpt();

				else :
					/**
					 * Commented out by FRB in version 2, as I think it is a mistake.
					 * Why would we want to register the cpt if load_cpt is not on.
					 */
					//add_action('init', [$this, 'CPT_Cranleigh_People']);
				endif;

				$this->load_in_all_cases();

			endif;

		}


		private function loadShortcodes()
		{
			return new Shortcodes();
		}

		public function load_if_cpt()
		{
			register_activation_hook(CRAN_PEOPLE_FILE_PATH, [Activate::class, 'activate']);

			CustomPostType::register();
			StaffCategoriesTaxonomy::register();
			Metaboxes::register();
			TGMPA::register();

			add_action('pre_get_posts', [$this, 'owd_post_order']);


			// Add Rest API Support
			new RestAPI();

		}

		public function load_in_all_cases()
		{
			add_action('after_setup_theme', [$this, 'profile_pictures']);

			if (is_admin()) {
				Admin::register();
			}

			add_action(
				'widgets_init',
				function () {

					// You can keep adding to this if you have added more class files
					// - just ensure that the name of the child class is what you put in as a registered widget.
					register_widget(__NAMESPACE__ . '\Cranleigh_People_Widget');
				}
			);

		}


		/**
		 * profile_pictures function.
		 *
		 * @access public
		 * @return void
		 */
		function profile_pictures()
		{
			add_image_size(self::PROFILE_PHOTO_SIZE_NAME, 400, 600, true);
		}


		function staff_roles()
		{

			$args = [
				'hide_empty' => false,
			];
			$terms = get_terms(StaffCategoriesTaxonomy::TAXONOMY_KEY, $args);
			$output = [];
			foreach ($terms as $role) {
				$output[ $role->slug ] = $role->name;
			}

			return $output;
		}


		// Order custom post types alphabetically

		function owd_post_order($query)
		{

			if ($query->is_post_type_archive(CustomPostType::POST_TYPE_KEY) && $query->is_main_query()) {

				$query->set('orderby', 'meta_value');
				$query->set('meta_key', 'staff_surname');
				$query->set('order', 'ASC');
			}

			return $query;
		}


	}



