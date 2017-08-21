<?php

namespace CranleighSchool\CranleighPeople;

/**
 * Class BaseController
 *
 * @package CranleighSchool\CranleighPeople
 */
class BaseController {

	/**
	 * @var bool
	 */
	public $load_from_blog_id = false;

	public $settings = [];

	/**
	 * BaseController constructor.
	 */
	public function __construct() {

		$plugin_data = get_plugin_data(CRAN_PEOPLE_FILE_PATH);
		$this->version = $plugin_data['Version'];

		$this->loadSettings();
		$this->setLoadFromBlogId();
	}

	/**
	 *
	 */
	private function loadSettings() {

		$load           = new Settings();
		$this->settings = get_option( 'cran_people_basic' );
	}

	/**
	 *
	 */
	public function setLoadFromBlogId() {

		if ( isset( $this->settings[ 'load_from_blog_id' ] ) ):
			$this->load_from_blog_id = $this->settings[ 'load_from_blog_id' ];
		else:
			$this->load_from_blog_id = BLOG_ID_CURRENT_SITE;
		endif;
	}

	/**
	 * @param string $variable
	 *
	 * @return mixed
	 */
	public function setting(string $variable ) {

		return $this->settings[ $variable ];
	}

	public function restore_current_blog() {

		if ( is_multisite() ):
			return restore_current_blog();
		endif;
	}

	public function switch_to_blog( $new_blog ) {

		if ( is_multisite() ):
			return switch_to_blog( $new_blog );
		endif;
	}

}
