<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\StaffHousesTaxonomy;

readonly class SetStaffHousesTaxonomy implements SetTaxaonomyInterface
{
    public function __construct(private \WP_Post $post, private PersonMap $person)
    {
    }

    public function handle(): void
    {
        wp_set_post_terms(
            $this->post->ID,
            $this->person->houses,
            StaffHousesTaxonomy::TAXONOMY_KEY
        );
    }
}
