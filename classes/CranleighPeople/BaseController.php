<?php
namespace CranleighPeople;

class BaseController {

	function __construct() {
		new Settings();
		
		$this->settings = get_option('cran_people_basic');	

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