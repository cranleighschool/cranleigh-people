<?php

namespace CranleighSchool\CranleighPeople\Exceptions;

	use CranleighSchool\CranleighPeople\Slacker;
	use Throwable;

class TooManyStaffFound extends SlackableException {

	public function __construct( $message = '', $code = 0, \WP_Query $wp_query = null, Throwable $previous = null ) {
		$slacker = new Slacker();
		$slacker->post( $message );
		parent::__construct( $message, $code, $wp_query, $previous );
	}
}
