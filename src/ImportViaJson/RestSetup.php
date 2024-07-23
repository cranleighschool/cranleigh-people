<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use WP_REST_Request;

class RestSetup
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerRoutes'));
    }

    public function registerRoutes(): void
    {
        register_rest_route(
            'people',
            'import',
            array(
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => new Import(),
                'permission_callback' => function (WP_REST_Request $request) {
                    return current_user_can('manage_options');
                },
            )
        );

        register_rest_route(
            'people',
            'photo/(?P<initials>[a-zA-Z]{2}[a-zA-Z0-9-]+)', // Allows for people like TS2. This is the regex for initials.
            array(
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => new ImportPhotoRoute(),
                'permission_callback' => function (WP_REST_Request $request) {
                    return current_user_can('manage_options');
                },
                'args' => [
                    'initials' => [
                        'required' => true,
                    ],
                    'last_updated' => [
                        'type' => 'string',
                        'format' => 'date-time',
                        'description' => 'the date the photo was last updated',
                        'required' => true,
                    ],
                ]
            )
        );
    }
}
