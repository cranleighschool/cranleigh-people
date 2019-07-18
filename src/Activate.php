<?php


	namespace CranleighSchool\CranleighPeople;


	class Activate
	{
		/**
		 * activate function. Called only once upon activation of the plugin on any site.
		 *
		 * @access public
		 * @return void
		 */

		public static function activate()
		{

			self::insert_staff_roles();
		}

		public static function insert_staff_roles()
		{

			$args = [];

			$roles = [
				'Head of Department',
				'Deputy Head',
				'Headmaster',
				'Housemaster / Housemistress',
				'Teacher',
				'Senior Management Team',
			];

			foreach ($roles as $role) :
				$test[] = wp_insert_term($role, StaffCategoriesTaxonomy::TAXONOMY_KEY);
			endforeach;
		}
	}
