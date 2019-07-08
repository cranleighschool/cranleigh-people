<?php
/*
	Plugin Name: Cranleigh People
	Plugin URI: http://www.cranleigh.org
	Description: One plugin that controls the people who work at Cranleigh.
	Author: Fred Bradley
	Version: 1.6.11
	Author URI: http://fred.im
*/

namespace CranleighSchool\CranleighPeople;

define( 'CRAN_PEOPLE_FILE_PATH', __FILE__ );

require_once 'vendor/autoload.php';

new Plugin( 'cranleigh-people' );
new Settings();