<?php

namespace CranleighSchool\CranleighPeople;

    if (! class_exists('CranleighSchool\CranleighPeople\Settings')) {
        class Settings
        {
            public const SETTINGS_SECTION_ID = 'cran_people_basic';
            private $settings_api;

            public static function register()
            {
                $instance = new self();
                $instance->settings_api = new WeDevsSettingsAPI();

                add_action('admin_init', [$instance, 'admin_init']);
                add_action('admin_menu', [$instance, 'admin_menu']);
            }

            public function admin_init()
            {

                // set the settings
                $this->settings_api->set_sections($this->get_settings_sections());
                $this->settings_api->set_fields($this->get_settings_fields());

                // initialize settings
                $this->settings_api->admin_init();
            }

            public function admin_menu()
            {
                add_options_page('Cranleigh People', 'Cranleigh People', 'manage_options', 'cranleigh_people_settings', [$this, 'plugin_page']);
            }

            public function get_settings_sections()
            {
                $sections = [
                    [
                        'id'    => self::SETTINGS_SECTION_ID,
                        'title' => __('Cranleigh People', 'cranleigh-2016'),
                    ],
                ];

                return $sections;
            }

            /**
             * Returns all the settings fields.
             *
             * @return array settings fields
             */
            public function get_settings_fields()
            {
                $settings_fields = [
                    'cran_people_basic' => [
                        [
                            'name'    => 'load_cpt',
                            'label'   => __('Load Custom Post Type', 'cranleigh-2016'),
                            'desc'    => __('Do you want to load the custom post type, or just the widgets and shortcodes?', 'cranleigh-2016'),
                            'type'    => 'radio',
                            'options' => [
                                'yes' => 'Yes, load everything.',
                                'no'  => 'No, I only need the widgets and shortcodes.',
                            ],
                            'default' => 'no',
                        ],
                        [
                            'name'    => 'isams_controlled',
                            'label'   => __('Under Isams Control', 'cranleigh-2016'),
                            'desc'    => __('If this instance under iSAMS control?', 'cranleigh-2016'),
                            'type'    => 'radio',
                            'options' => [
                                'yes' => 'Yes, it is',
                                'no'  => 'No it\'s not, please let me edit things like a normal Wordpress post',
                            ],
                        ],

                        [
                            'name'              => 'default_photo_attachment_id',
                            'label'             => __('Default Photo Attachment ID', 'cranleigh-2016'),
                            'desc'              => 'The attachment ID of the photo you want to use for the default photo',
                            'type'              => 'text',
                            'sanitize_callback' => 'intval',
                            'default'           => 32492,
                        ],

                        [
                            'name'    => 'load_from_blog_id',
                            'label'   => 'Which Blog ID to load from',
                            'desc'    => "Which site did you want to grab data from? (eg. for Houses you will mostly put &quot;Cranleigh School&quot; but for Cranleigh Abu Dhabi Site, you'll need to put their site!)",
                            'type'    => 'radio',
                            'default' => 1,
                            'options' => $this->selectSite_optionList(),
                        ],
                        [
                            'name'    => 'slack_webhook_endpoint',
                            'label'   => 'Slack Webhook Endpoint',
                            'desc'    => 'The Slack Webhook',
                            'type'    => 'text',
                            'default' => null,
                        ],
                        [
                            'name' => 'importer_api_endpoint',
                            'label' => 'Importer API Endpoint (full url)',
                            'desc' => 'The full url of the People Manager endpoint',
                            'type' => 'url',
                        ],

                    ],
                ];

                return $settings_fields;
            }

            public function get_sites()
            {
                if (is_multisite() === false) {
                    return new \WP_Error('Not A MultiSite', "This is not a multi site therefore I can't let you call a function that won't exist!");
                }

                $subsites = get_sites();
                $output = [];
                foreach ($subsites as $subsite) {
                    $subsite_id = get_object_vars($subsite)['blog_id'];
                    $subsite_name = get_blog_details($subsite_id)->blogname;
                    $subsite->name = $subsite_name;
                    $output[] = $subsite;
                }

                return $output;
            }

            public function selectSite_optionList()
            {
                $sites = $this->get_sites();
                if (is_wp_error($sites)) {
                    return [1 => 'This is not a multisite - so you have no choice here!'];
                }

                $list = [];
                foreach ($this->get_sites() as $site) {
                    $list[$site->blog_id] = $site->name;
                }

                return $list;
            }

            public function plugin_page()
            {
                echo '<div class="wrap">';

                $this->settings_api->show_navigation();
                $this->settings_api->show_forms();

                echo '</div>';
            }

            /**
             * Get all the pages.
             *
             * @return array page names with key value pairs
             */
            public function get_pages()
            {
                $pages = get_pages();
                $pages_options = [];
                if ($pages) {
                    foreach ($pages as $page) {
                        $pages_options[$page->ID] = $page->post_title;
                    }
                }

                return $pages_options;
            }
        }
    }
