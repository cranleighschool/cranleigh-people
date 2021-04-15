<?php

namespace CranleighSchool\CranleighPeople\Importer;

    class Person
    {
        private $_data;

        public function __construct(\stdClass $person)
        {
            $this->_data = $person;

            $this->_data->title = $person->title->name;
        }

        public function __set($property, $value)
        {
            return $this->_data->{$property} = $value;
        }

        public function __get($property)
        {
            return array_key_exists($property, $this->_data) ? $this->_data->{$property} : null;
        }

        /**
         * @deprecated No longer used in application. To be removed.
         * @return bool
         */
        public static function setup()
        {
            $request = wp_remote_get('https://people.cranleigh.org/api/v1/people/1');

            $contentType = wp_remote_retrieve_header($request, 'content-type');

            if ($contentType !== 'application/json') {
                // not what we are expecting, duck out early with a wp error and exception?
                return false;
            }

            $body = wp_remote_retrieve_body($request);

            $result = json_decode($body);
        }
    }
