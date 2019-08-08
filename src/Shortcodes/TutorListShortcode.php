<?php


	namespace CranleighSchool\CranleighPeople\Shortcodes;


	use CranleighSchool\CranleighPeople\View;

	class TutorListShortcode extends BaseShortcode
	{
		protected function tagName(): string
		{
			return 'card_list';
		}

		public function handle(array $atts, $content = NULL)
		{
			$a = shortcode_atts(
				[
					'people'  => NULL,
					'columns' => 2,
					'type'    => 'small',
					'sort'    => 'surname',
				],
				$atts
			);

			switch ($a['columns']) :
				case 2:
					$class = 6;
					break;
				case 3:
					$class = 4;
					break;
				default:
					$class = 6;
					break;
			endswitch;

			$people = array_map("trim", explode(',', $a['people']));

//			if ($a['sort']==='surname') {
			$staff = self::get_wp_query_from_usernames($people);
//			} else {
//				$staff = [];

//			}
			return View::render('tutor-list', compact('staff', 'class'));
		}
	}
