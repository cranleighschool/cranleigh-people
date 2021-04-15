<?php

namespace CranleighSchool\CranleighPeople\Shortcodes;

    interface ShortcodeInterface
    {
        /**
         * How we register the shortcode without a __construct method.
         */
        public static function register(): void;

        /**
         * @param array $atts
         * @param null  $content
         *
         * @return mixed
         */
        public function handle(array $atts, $content = null);
    }
