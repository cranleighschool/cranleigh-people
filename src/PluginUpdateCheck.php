<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 21/08/2017
 * Time: 08:52
 */

namespace CranleighSchool\CranleighPeople;

use Puc_v4_Factory;

class PluginUpdateCheck {

	public function __construct( string $plugin_name, string $user = 'cranleighschool' ) {
		$this->plugin_name = $plugin_name;
		$this->user        = $user;
		$this->file        = CRAN_PEOPLE_FILE_PATH;
		$this->update_check( $plugin_name, $user );
	}

	private function update_check( string $plugin_name, string $user ) {

		$updateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/' . $user . '/' . $plugin_name . '/',
			CRAN_PEOPLE_FILE_PATH,
			$plugin_name
		);

		$updateChecker->setBranch( 'master' );

	}
}
