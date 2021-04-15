<?php

namespace CranleighSchool\CranleighPeople;

class Deactivate {

	public function deactivate() {
		wp_clear_scheduled_hook( Cron::SYNC_CRONJOB_NAME );
	}
}
