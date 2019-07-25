<?php

	namespace CranleighSchool\CranleighPeople\Shortcodes;

	use CranleighSchool\CranleighPeople\Traits\ShortcodeTrait;
	/**
	 * Class BaseShortcode
	 *
	 * @package CranleighSchool\CranleighPeople\Shortcodes
	 */
	abstract class BaseShortcode implements ShortcodeInterface
	{
		use ShortcodeTrait;

		/**
		 * BaseShortcode constructor.
		 */
		public function __construct()
		{
			$this->tag = $this->tagName();
		}

		/**
		 * Returns the name of the shortcode tag
		 *
		 * @return string
		 */
		abstract protected function tagName(): string;

		/**
		 *
		 */
		public static function register(): void
		{
			$instance = new static();
			add_shortcode($instance->tagName(), [$instance, 'handle']);
		}

		/**
		 * @return string
		 */
		public function __toString()
		{
			return $this->tagName();
		}


	}
