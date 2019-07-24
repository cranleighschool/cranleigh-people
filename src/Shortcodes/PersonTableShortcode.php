<?php

	namespace CranleighSchool\CranleighPeople\Shortcodes;

	use CranleighSchool\CranleighPeople\View;

	/**
	 * Class PersonTableShortcode
	 *
	 * @package CranleighSchool\CranleighPeople\Shortcodes
	 */
	class PersonTableShortcode extends BaseShortcode
	{
		/**
		 * @param array $atts
		 * @param null  $content
		 *
		 * @return mixed|string
		 */
		public function handle(array $atts, $content = NULL)
		{
			$atts = shortcode_atts(
				[
					'people'        => NULL,
					'with_headers' => false,
				],
				$atts
			);

			$all_users = explode(',', $atts['people']);

			$users = [];

			foreach ($all_users as $user) :
				$users[] = preg_replace('/[^A-Za-z]/', '', trim($user));
			endforeach;

			$staff = self::get_wp_query_from_usernames($users);

			return View::render('table-list', compact('staff', 'atts', 'users'));
		}

		/**
		 * @return string
		 */
		protected function tagName(): string
		{
			return 'person_table';
		}
	}
