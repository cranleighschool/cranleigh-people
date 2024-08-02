<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\Plugin;
use WP_REST_Request;

class RestSetup
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerRoutes'));
    }
    private function permissions(WP_REST_Request $request): bool
    {
        $ip = self::get_client_ip();
        error_log("IP: $ip");

        return current_user_can('manage_options') && $ip === Plugin::getPluginSetting('ip_allowlist');
    }
    public static function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // Check if IP is passed from shared internet
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Check if IP is passed from proxy
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Get the remote IP address
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        return $ip_address;
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
                    return $this->permissions($request);
                },
            )
        );

        register_rest_route(
            'people',
            'photo/(?P<initials>[a-zA-Z]{2}[a-zA-Z0-9.]*)', // Allows for people like TS2. This is the regex for initials.
            array(
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => new ImportPhotoRoute(),
                'permission_callback' => function (WP_REST_Request $request) {
                    return $this->permissions($request);
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
