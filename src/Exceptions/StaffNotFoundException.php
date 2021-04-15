<?php

namespace CranleighSchool\CranleighPeople\Exceptions;

	use Throwable;

class StaffNotFoundException extends \Exception {

	public function __construct( $message = '', $code = 0, \WP_Query $wp_query = null, Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}
}
