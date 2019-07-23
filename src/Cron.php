<?php


	namespace CranleighSchool\CranleighPeople;


	use Carbon\Carbon;
	use Carbon\CarbonInterface;

	class Cron
	{
		public CONST SYNC_CRONJOB_NAME = "cranleigh_people_daily_sync";

		public static function setup_sync_cronjob() {
			if (! wp_next_scheduled ( self::SYNC_CRONJOB_NAME )) {
				wp_schedule_event(time(), 'daily', self::SYNC_CRONJOB_NAME);
			}
		}
		public static function next_scheduled_sync() {
			$date_next_scheduled = Carbon::createFromTimestamp(wp_next_scheduled(self::SYNC_CRONJOB_NAME), get_option('timezone_string'));

			$humanDiff = $date_next_scheduled->diffForHumans(Carbon::now(get_option('timezone_string')), CarbonInterface::DIFF_RELATIVE_TO_NOW );

			return sprintf('%s [%s]', $humanDiff, $date_next_scheduled->format("Y-m-d H:i:s"));
		}
	}
