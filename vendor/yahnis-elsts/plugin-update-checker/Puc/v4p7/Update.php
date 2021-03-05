<?php

if (! class_exists('Puc_v4p7_Update', false)) {

    /**
     * A simple container class for holding information about an available update.
     *
     * @author Janis Elsts
     */
    abstract class Puc_v4p7_Update extends Puc_v4p7_Metadata
    {
        public $slug;
        public $version;
        public $download_url;
        public $translations = [];

        /**
         * @return string[]
         */
        protected function getFieldNames()
        {
            return ['slug', 'version', 'download_url', 'translations'];
        }

        public function toWpFormat()
        {
            $update = new stdClass();

            $update->slug = $this->slug;
            $update->new_version = $this->version;
            $update->package = $this->download_url;

            return $update;
        }
    }
}
