<?php
/*
	Plugin Name: Cranleigh People
	Plugin URI: http://www.cranleigh.org
	Description: One plugin that controls the people who work at Cranleigh.
	Author: Fred Bradley
	Version: 1.3.4
	Author URI: http://fred.im
*/

namespace CranleighPeople;

define("CRAN_PEOPLE_FILE_PATH", __FILE__);

spl_autoload_register(function ($class) {
	if ( false === strpos( $class, 'CranleighPeople' ) ) {
		return;
	}
	$filename = dirname(__FILE__) . '/classes/' . str_replace('\\', '/', $class) . '.php';
	require_once($filename);
});

require_once(dirname(__FILE__).'/extras/class-tgm-plugin-activation.php');

new Plugin();
