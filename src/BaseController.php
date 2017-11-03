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

	public function __construct() {
	}

	public function load() {

		$this->loadSettings();
		$this->setLoadFromBlogId();
	}

	/**
	 *
	 */
	private function loadSettings() {

		//$load           = new Settings();
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

	public function get_permalink(int $post_id) {

		if ( is_multisite() ):
			return get_blog_permalink($this->load_from_blog_id, $post_id);
		else:
			return $this->get_permalink($post_id);
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

	public static function restore_current_blog() {

		if ( is_multisite() ):
			return restore_current_blog();
		endif;
	}

	public static function switch_to_blog( $new_blog ) {

		if ( is_multisite() ):
			return switch_to_blog( $new_blog );
		endif;
	}

}
