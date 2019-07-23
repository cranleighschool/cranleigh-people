<?php


	namespace CranleighSchool\CranleighPeople;


	class Cron
	{
		public CONST SYNC_CRONJOB_NAME = "cranleigh_people_daily_sync";

		public static function setup_sync_cronjob() {
			if (! wp_next_scheduled ( self::SYNC_CRONJOB_NAME )) {
				wp_schedule_event(time(), 'daily', self::SYNC_CRONJOB_NAME);
			}
		}
	}
