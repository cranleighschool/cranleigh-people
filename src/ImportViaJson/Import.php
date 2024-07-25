<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use Exception;
use WP_REST_Request;

class Import
{
    /**
     * @throws Exception
     */
    public function __invoke(WP_REST_Request $data): array
    {
        $dataReceived = $data->get_body();
        $objData = json_decode($dataReceived, true);

        $people = array_map(function ($person) {
            return new PersonMap($person);
        }, $objData['data']);

        $result = [];
        foreach ($people as $person) {
            $result[] = (new ImportPerson($person))->handle();
        }
        return [
            'from' => $this->get_client_ip(),
            'success' => $result,

        ];
    }

    private function get_client_ip() {
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
}
