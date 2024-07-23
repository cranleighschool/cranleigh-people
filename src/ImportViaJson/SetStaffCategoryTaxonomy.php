<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\StaffCategoriesTaxonomy;

readonly class SetStaffCategoryTaxonomy implements SetTaxaonomyInterface
{

    public function __construct(private \WP_Post $post, private PersonMap $person)
    {
    }

    public function handle(): void
    {
        $staff_categories = $this->sanitize($this->person->roles, $this->person->departments);

        wp_set_post_terms(
            $this->post->ID,
            $staff_categories,
            StaffCategoriesTaxonomy::TAXONOMY_KEY
        );
    }

    /**
     * staff_categories function.
     *
     *
     * @param array $roles
     * @param array $departments
     *
     * @return array
     */
    private function sanitize(array $roles, array $departments): array
    {
        $staff_categories = array();

        // Roles
        if (preg_grep('/teacher/i', $roles)) {
            $staff_categories[] = 'teacher';
        }
        if (preg_grep('/school governor/i', $roles)) {
            $staff_categories[] = 'school-governor';
        }
        if (preg_grep('/head of department/i', $roles)) {
            $staff_categories[] = 'head-of-department';
        }
        if (preg_grep('/senior management team/i', $roles)) {
            $staff_categories[] = 'smt';
        }
        if (preg_grep('/housemaster\/mistress/i', $roles)) {
            if (preg_grep('/deputy/i', $roles)) {
                $staff_categories[] = 'deputy-housemaster-housemistress';
            } else {
                $staff_categories[] = 'housemaster-housemistress';
            }
        }
        if (preg_grep('/vmt/i', $roles)) {
            $staff_categories[] = 'visiting-music-teachers';
        }

        // Departments
        if (preg_grep('/accounts/i', $departments)) {
            $staff_categories[] = 'accounts';
        }
        if (preg_grep('/bursarial/i', $departments)) {
            $staff_categories[] = 'bursarial';
        }
        if (preg_grep('/catering/i', $departments)) {
            $staff_categories[] = 'catering';
        }
        if (preg_grep('/domestic services/i', $departments)) {
            $staff_categories[] = 'domestic-services';
        }
        if (preg_grep('/grounds/i', $departments)) {
            $staff_categories[] = 'grounds';
        }
        if (preg_grep('/hr/i', $departments)) {
            $staff_categories[] = 'hr';
        }
        if (preg_grep('/it support/i', $departments)) {
            $staff_categories[] = 'it-support';
        }

        return $staff_categories;
    }
}
