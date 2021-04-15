<?php

namespace CranleighSchool\CranleighPeople\Shortcodes;

    use CranleighSchool\CranleighPeople\View;

    /**
     * Class PersonTableShortcode.
     */
    class PersonTableShortcode extends BaseShortcode
    {
        /**
         * @param array $atts
         * @param null  $content
         *
         * @return mixed|string
         */
        public function handle(array $atts, $content = null)
        {
            $atts = shortcode_atts(
                [
                    'people'        => null,
                    'users' => null, //Backwards compatibility
                    'with_headers' => false,
                ],
                $atts
            );
            /**
             * For backwards compatibility. (Before version 2, we used "users" as the parameter name).
             */
            if ($atts['people'] === null && $atts['users'] !== null) {
                $atts['people'] = $atts['users'];
            }

            $all_users = explode(',', $atts['people']);

            $users = [];

            foreach ($all_users as $user) {
                $users[] = preg_replace('/[^A-Za-z]/', '', trim($user));
            }

            $staff = self::get_wp_query_from_usernames($users);

            return View::render('table-list', compact('staff', 'atts', 'users'));
        }

        /**
         * @return string
         */
        protected function tagName(): string
        {
            return 'person_table';
        }
    }
