<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\Exceptions\StaffNotFoundException;
use CranleighSchool\CranleighPeople\Exceptions\TooManyStaffFound;
use CranleighSchool\CranleighPeople\Metaboxes;
use CranleighSchool\CranleighPeople\Plugin;

class FindStaffPost
{
    public function __construct(private readonly string $school_initials)
    {
    }

    /**
     * @throws StaffNotFoundException
     * @throws TooManyStaffFound
     */
    public function find(): \WP_Post
    {
        $args = array(
            'post_type' => Plugin::POST_TYPE_KEY,
            'posts_per_page' => -1,
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => Metaboxes::fieldID('username'),
                    'value' => $this->school_initials,
                    'compare' => '=',
                ),
            ),
        );

        $posts = new \WP_Query($args);

        if ($posts->have_posts()) {
            if ($posts->found_posts > 1) {
                // too many posts
                throw new TooManyStaffFound('Too Many Staff Members Found matching ' . $this->school_initials, 400);
            }
        } else {
            // No Matching Post Found
            throw new StaffNotFoundException('No staff member found matching ' . $this->school_initials, 404);
        }

        while ($posts->have_posts()) {
            $posts->the_post();
            $staff_post = get_post(get_the_ID());
        }
        wp_reset_query();
        wp_reset_postdata();

        return $staff_post;
    }

}
