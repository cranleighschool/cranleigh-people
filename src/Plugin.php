<?php

namespace CranleighSchool\CranleighPeople;

	use CranleighSchool\CranleighPeople\Importer\Importer;

class Plugin extends BaseController {

	public const POST_TYPE_KEY = 'staff';
	public const PROFILE_PHOTO_SIZE_NAME = 'staff-photo';
	public $isams_controlled = false;
    public $withCron = false;

	private $load_cpt = false;

	/**
	 * __construct function.
	 *
	 * @return void
	 */
	public function __construct( string $plugin_name ) {
		parent::__construct( $plugin_name );

		$this->load();

		$this->loadShortcodes();

		if ( isset( $this->settings['isams_controlled'] ) && $this->settings['isams_controlled'] == 'yes' ) {
			$this->isams_controlled = true;
		} else {
			$this->isams_controlled = false;
		}
		if (isset($this->settings['disable_wp_cron']) && $this->settings['disable_wp_cron'] == 'yes') {
			$this->withCron = false;
		} else {
			$this->withCron = true;
		}

		if ($this->withCron === false) {
			add_action('after_setup_theme', function() {
				wp_clear_scheduled_hook(Cron::SYNC_CRONJOB_NAME);
			});
		}
		if ($this->withCron === true) {
			Cron::setup_sync_cronjob();
		}

		if ( isset( $this->settings['load_cpt'] ) ) {
			if ( $this->settings['load_cpt'] == 'yes' ) {
				$this->load_cpt = true;

				$this->load_if_cpt();
			} else {
				/**
				 * Commented out by FRB in version 2, as I think it is a mistake.
				 * Why would we want to register the cpt if load_cpt is not on.
				 */
				//add_action('init', [$this, 'CPT_Cranleigh_People']);
			}

			$this->load_in_all_cases();
		}
	}

	private function loadShortcodes() {
		return new Shortcodes();
	}

	private function load_if_cpt() {
		register_activation_hook( CRAN_PEOPLE_FILE_PATH, array( Activate::class, 'activate' ) );
		register_deactivation_hook( CRAN_PEOPLE_FILE_PATH, array( Deactivate::class, 'deactivate' ) );

		CustomPostType::register();
		StaffCategoriesTaxonomy::register();
		StaffHousesTaxonomy::register();
		StaffSubjectsTaxonomy::register();
		Metaboxes::register();
		TGMPA::register();

		add_action( 'pre_get_posts', array( $this, 'owd_post_order' ) );

		// Add Rest API Support
		new RestAPI();
	}

	private function load_in_all_cases() {
		add_action( 'after_setup_theme', array( $this, 'profile_pictures' ) );

		if ( is_admin() ) {
			Admin::register();
		}

		add_action(
			'widgets_init',
			function () {

				// You can keep adding to this if you have added more class files
				// - just ensure that the name of the child class is what you put in as a registered widget.
				register_widget( __NAMESPACE__ . '\Cranleigh_People_Widget' );
			}
		);

		add_action( Cron::SYNC_CRONJOB_NAME, array( Importer::class, 'import' ) );
	}

	/**
	 * Retrieves the IMG Tag for the "Staff Profile Photo" sized image of the person.
	 *
	 * @param string      $username
	 * @param string|null $output_type "ID" for the attachment_id, or "URL" for the image url. Default: null
	 *
	 * @return false|int|string
	 * @throws \Exception
	 */
	public static function getMugshotOf( string $username, string $output_type = null ) {
		remove_filter( 'post_thumbnail_html', 'cranleigh_post_thumbnail_fallback' );

		$staff_post = Importer::find_wp_staff_post( $username );

		if ( strtoupper( $output_type ) === 'URL' ) {
			return get_the_post_thumbnail_url( $staff_post, self::PROFILE_PHOTO_SIZE_NAME );
		} elseif ( $output_type == 'id' ) {
			$thumbnail_id = get_post_thumbnail_id( $staff_post );

			return ( $thumbnail_id === '' ) ? 0 : (int) $thumbnail_id;
		}

		return get_the_post_thumbnail( $staff_post, self::PROFILE_PHOTO_SIZE_NAME );
	}

	/**
	 * profile_pictures function.
	 *
	 * @return void
	 */
	public function profile_pictures() {
		add_image_size( self::PROFILE_PHOTO_SIZE_NAME, 400, 600, true );
	}

	public function staff_roles() {
		$args = array(
			'hide_empty' => false,
		);
		$terms = get_terms( StaffCategoriesTaxonomy::TAXONOMY_KEY, $args );
		$output = array();
		foreach ( $terms as $role ) {
			$output[ $role->slug ] = $role->name;
		}

		return $output;
	}

	// Order custom post types alphabetically

	public function owd_post_order( $query ) {
		if ( $query->is_post_type_archive( CustomPostType::POST_TYPE_KEY ) && $query->is_main_query() ) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'staff_surname' );
			$query->set( 'order', 'ASC' );
		}

		return $query;
	}
}
