<?php


	namespace CranleighSchool\CranleighPeople\Exceptions;


	use CranleighSchool\CranleighPeople\Traits\SlackableExceptionTrait;
	use Throwable;

	abstract class SlackableException extends \Exception
	{
		use SlackableExceptionTrait;

		public function __construct($message = "", $code = 0, \WP_Query $wp_query, Throwable $previous = NULL)
		{
			self::slackPost($message);
			parent::__construct($message, $code, $previous);
		}
	}
