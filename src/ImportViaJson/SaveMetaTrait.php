<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use CranleighSchool\CranleighPeople\Metaboxes;

trait SaveMetaTrait
{
    /**
     * @param string $fieldName
     * @param          $value
     *
     * @return void
     */
    private function saveMeta(string $fieldName, $value): void
    {
        update_post_meta($this->post->ID, Metaboxes::fieldID($fieldName), $value);
    }
}
