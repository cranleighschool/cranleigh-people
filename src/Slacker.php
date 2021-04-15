<?php

/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 30/08/2017
 * Time: 14:42.
 */

namespace CranleighSchool\CranleighPeople;

    class Slacker extends \FredBradley\CranleighSlacker\Slacker
    {
        public static $room = 'it-cranleigh-people';
        private static $webhookEndpoint;

        public function __construct()
        {
            self::$webhookEndpoint = Plugin::getPluginSetting('slack_webhook_endpoint');
            if (defined('APP_ENV') && APP_ENV == 'local') {
                self::$room = 'development-app-logs';
            }

            parent::__construct(self::$webhookEndpoint, self::$room);
        }
    }
