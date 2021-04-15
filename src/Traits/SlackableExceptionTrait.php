<?php

namespace CranleighSchool\CranleighPeople\Traits;

	use CranleighSchool\CranleighPeople\Slacker;

trait SlackableExceptionTrait {

	private static function slackPost( string $message ) {
		$slacker = new Slacker();
		$slacker->post( $message );
		error_log( 'Should have written to Slack! (' . $message . ')' );
	}
}
