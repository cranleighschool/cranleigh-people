<?php


	namespace CranleighSchool\CranleighPeople;


	class Activate
	{
		public CONST SYNC_CRONJOB_NAME = "cranleigh_people_daily_sync";
		/**
		 * activate function. Called only once upon activation of the plugin on any site.
		 *
		 * @access public
		 * @return void
		 */

		public static function activate()
		{

			self::insert_staff_roles();

			self::setup_sync_cronjob();
		}

		public static function setup_sync_cronjob() {
			if (! wp_next_scheduled ( self::SYNC_CRONJOB_NAME )) {
				wp_schedule_event(time(), 'daily', self::SYNC_CRONJOB_NAME);
			}
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
