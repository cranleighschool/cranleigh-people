<?php

	namespace CranleighSchool\CranleighPeople\Exceptions;

	use CranleighSchool\CranleighPeople\Slacker;
	use Throwable;

	class StaffNotFoundException extends \Exception
	{

		public function __construct($message = "", $code = 0, Throwable $previous = NULL)
		{
			$slacker = new Slacker();
			$slacker->post($message);
			parent::__construct($message, $code, $previous);
		}

	}
