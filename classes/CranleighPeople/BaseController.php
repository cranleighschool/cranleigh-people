<?php
namespace CranleighPeople;

new Settings();

class BaseController {
	public $load_from_blog_id = false;

	function __construct() {

		$this->settings = get_option('cran_people_basic');

		$this->setLoadFromBlogId();
	}

	function setLoadFromBlogId() {
		if (isset($this->settings['load_from_blog_id'])):
			$this->load_from_blog_id = $this->settings['load_from_blog_id'];
		else:
			$this->load_from_blog_id = BLOG_ID_CURRENT_SITE;
		endif;
	}

	function setting($variable) {
		return $this->settings[$variable];
	}

	function restore_current_blog() {
		if (is_multisite()):
			return restore_current_blog();
		endif;
	}

	function switch_to_blog($new_blog) {
		if (is_multisite()):
			return switch_to_blog($new_blog);
		endif;
	}

}
