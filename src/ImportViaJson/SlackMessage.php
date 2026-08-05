<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\Slacker;

class SlackMessage
{
    public function __construct(private readonly string $message)
    {
    }

    public function send(): void
    {
        $slacker = new Slacker();
        $slacker->setUsername('Cranleigh People Importer');
        $slacker->post($this->message);
    }

}
