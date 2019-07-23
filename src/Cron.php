<?php


	namespace CranleighSchool\CranleighPeople;


	use Carbon\Carbon;
	use Carbon\CarbonInterface;

	/**
	 * Class Cron
	 *
	 * @package CranleighSchool\CranleighPeople
	 */
	class Cron
	{
		public static $timezone_string;

		/**
		 *
		 */
		public CONST SYNC_CRONJOB_NAME = "cranleigh_people_daily_sync";

		/**
		 * Callback which sets up the scheduled event.
		 */
		public static function setup_sync_cronjob() {
			self::$timezone_string = get_option('timezone_string');
			if (! wp_next_scheduled ( self::SYNC_CRONJOB_NAME )) {
				wp_schedule_event(Carbon::parse("4am", self::$timezone_string)->getTimestamp(), 'daily', self::SYNC_CRONJOB_NAME);
			}
		}

		/**
		 * @return string
		 * @throws \Exception
		 */
		public static function next_scheduled_sync() {
			self::$timezone_string = get_option('timezone_string');

			$date_next_scheduled = Carbon::createFromTimestamp(wp_next_scheduled(self::SYNC_CRONJOB_NAME), self::$timezone_string);

			$humanDiff = $date_next_scheduled->diffForHumans(Carbon::now(self::$timezone_string), CarbonInterface::DIFF_RELATIVE_TO_NOW );

			return sprintf('%s [%s]', $humanDiff, $date_next_scheduled->format("Y-m-d H:i:s"));
		}
	}
