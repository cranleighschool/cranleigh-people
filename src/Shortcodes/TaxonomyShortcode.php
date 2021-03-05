<?php

namespace CranleighSchool\CranleighPeople\Shortcodes;

    use CranleighSchool\CranleighPeople\Metaboxes;
    use CranleighSchool\CranleighPeople\StaffCategoriesTaxonomy;
    use CranleighSchool\CranleighPeople\View;
    use WP_Query;

    class TaxonomyShortcode extends BaseShortcode
    {
        protected function tagName(): string
        {
            return 'people_taxonomy';
        }

        public function handle(array $atts, $content = null)
        {
            $atts = shortcode_atts(
                [
                    'taxonomy' => null,
                ],
                $atts
            );
            $args = [
                'orderby'        => 'meta_value',
                'meta_key'       => Metaboxes::fieldID('surname'),
                'order'          => 'ASC',
                'tax_query' => [
                    [
                        'taxonomy' => StaffCategoriesTaxonomy::TAXONOMY_KEY,
                        'field'    => 'slug',
                        'terms'    => $atts['taxonomy'],
                    ],
                ],
            ];

            $staff = new WP_Query(wp_parse_args($args, $this->query_args));

            if ($staff->post_count === 0) {
                $parsed_atts = implode(', ', array_map(
                    function ($v, $k) {
                        if (is_array($v)) {
                            return $k.'[]='.implode('&'.$k.'[]=', $v);
                        } else {
                            return $k.'='.$v;
                        }
                    },
                    $atts,
                    array_keys($atts)
                ));

                return View::render('404', compact('parsed_atts'));
            }

            return View::render('table-list', compact('staff', 'atts'));
        }
    }
