<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 30/08/2017
 * Time: 14:42
 */

namespace CranleighSchool\CranleighPeople;

class Slacker extends \FredBradley\CranleighSlacker\Slacker {

	private static $webhookEndpoint = 'https://hooks.slack.com/services/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';

	public static $room = 'website-project';

	public function __construct() {
		parent::__construct( self::$webhookEndpoint, self::$room );
	}
}
