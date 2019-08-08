<?php

	namespace CranleighSchool\CranleighPeople\Exceptions;

	use CranleighSchool\CranleighPeople\Slacker;
	use Throwable;

	class TooManyStaffFound extends \Exception
	{
		public function __construct($message = "", $code = 0, \WP_Query $wp_query = NULL, Throwable $previous = NULL)
		{
			$slacker = new Slacker();
			$slacker->post($message);
			parent::__construct($message, $code, $previous);
		}
	}
