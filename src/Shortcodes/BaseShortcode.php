<?php

namespace CranleighSchool\CranleighPeople\Shortcodes;

    use CranleighSchool\CranleighPeople\Traits\ShortcodeTrait;

    /**
     * Class BaseShortcode.
     */
    abstract class BaseShortcode implements ShortcodeInterface
    {
        use ShortcodeTrait;

        public $tag;

        /**
         * BaseShortcode constructor.
         */
        public function __construct()
        {
            $this->tag = $this->tagName();
        }

        /**
         * Returns the name of the shortcode tag.
         *
         * @return string
         */
        abstract protected function tagName(): string;

        /**
         * Static method that registers the shortcode.
         */
        public static function register(): void
        {
            $instance = new static();
            add_shortcode($instance->tagName(), [$instance, 'handle']);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->tagName();
        }
    }
