<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\StaffSubjectsTaxonomy;

readonly class SetStaffSubjectsTaxonomy implements SetTaxaonomyInterface
{
    public function __construct(private \WP_Post $post, private PersonMap $person)
    {
    }

    public function handle(): void
    {
        $unsets = array('House', 'Deputy House', 'Tutor Period', 'Guided Reading', 'Read', 'Prep');
        $subjects = array(); // gotta do it this way because of overloaded property errors.

        foreach ($this->person->subjects as $subject) {
            if (!in_array($subject, $unsets)) {
                $subjects[] = $subject;
            }
        }

        wp_set_post_terms(
            $this->post->ID,
            $subjects,
            StaffSubjectsTaxonomy::TAXONOMY_KEY
        );

    }
}
