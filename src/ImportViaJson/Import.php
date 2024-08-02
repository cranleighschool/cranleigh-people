<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\Plugin;
use Exception;
use WP_REST_Request;

class Import
{
    /**
     * @throws Exception
     */
    public function __invoke(WP_REST_Request $data): array
    {
        if (Plugin::getPluginSetting('isams_controlled') !== 'yes') {
            throw new \Exception('The plugin is not set to be under ISAMS Control', 400);
        }

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
        return RestSetup::get_client_ip();
    }
}
