<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

interface SetTaxaonomyInterface
{
    public function __construct(\WP_Post $post, PersonMap $person);

    public function handle(): void;
}
