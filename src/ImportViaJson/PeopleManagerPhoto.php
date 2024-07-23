<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

class PeopleManagerPhoto
{
    public function __construct(public string $filename, public string $name, public string $binary)
    {

    }
}
