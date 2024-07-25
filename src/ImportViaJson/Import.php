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

        return $result;
    }
}
