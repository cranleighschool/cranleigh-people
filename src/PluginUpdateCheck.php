<?php

namespace CranleighSchool\CranleighPeople;

use Puc_v4_Factory;

/**
 * Class PluginUpdateCheck
 *
 * @package CranleighSchool\CranleighPeople
 */
class PluginUpdateCheck {

	/**
	 * PluginUpdateCheck constructor.
	 *
	 * @param string $plugin_name
	 * @param string $user
	 */
	public function __construct( string $plugin_name, string $user = 'cranleighschool' ) {
		$this->plugin_name = $plugin_name;
		$this->user        = $user;
		$this->file        = CRAN_PEOPLE_FILE_PATH;
		$this->update_check( $plugin_name, $user );
	}

	/**
	 * @param string $plugin_name
	 * @param string $user
	 */
	private function update_check( string $plugin_name, string $user ) {

		$update_checker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/' . $user . '/' . $plugin_name . '/',
			CRAN_PEOPLE_FILE_PATH,
			$plugin_name
		);

		$update_checker->setBranch( 'master' );

	}
}
