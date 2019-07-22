<?php


	namespace CranleighSchool\CranleighPeople;


	class Deactivate
	{
		public function deactivate()
		{
			wp_clear_scheduled_hook(Activate::SYNC_CRONJOB_NAME);
		}
	}
