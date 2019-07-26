<?php

	namespace CranleighSchool\CranleighPeople\Shortcodes;

	use CranleighSchool\CranleighPeople\View;

	class DynamicTableListShortcode extends BaseShortcode
	{
		public function handle(array $atts, $content = NULL)
		{
			$a = shortcode_atts(
				[
					'people'       => NULL,
					'users'        => NULL, // backward compatibility
					'class'        => 'table-striped',
					'first_column' => 'full_title',
					'last_column'  => 'email_address',
					'sort'         => false,
				],
				$atts
			);

			/**
			 * For backwards compatibility. (Before version 2, we used "users" as the parameter name)
			 */
			if ($a['people'] === NULL && $a['users'] !== NULL) {
				$a['people'] = $a['users'];
			}

			$people = explode(',', $a['people']);

			$users = [];
			foreach ($people as $person) :
				$initial = str_split($person);
				$last = end($initial);
				$users[ $person ] = $last;
			endforeach;

			if ($a['sort'] == true) {
				asort($users);
			}

			$first_column = $a['first_column'];
			$last_column = $a['last_column'];

			return View::render('dynamic-table-list', compact('users', 'a', 'first_column', 'last_column'));
		}


		protected function tagName(): string
		{
			return 'table_list';
		}
	}
