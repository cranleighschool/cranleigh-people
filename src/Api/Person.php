<?php

namespace CranleighSchool\CranleighPeople\Api;

    use CranleighSchool\CranleighPeople\Metaboxes;
    use CranleighSchool\CranleighPeople\Plugin;

    /**
     * Class Person.
     */
    class Person
    {
        /**
         * @var false|int
         */
        public $post_id;

        /**
         * @var string
         */
        public $post_slug;

        /**
         * @var string
         */
        public $adult_name;

        /**
         * @var mixed
         */
        public $school_name;

        /**
         * @var mixed
         */
        public $jobtitle;
        /**
         * @var string
         */
        public $biography;
        /**
         * @var false|string
         */
        public $permalink;
        /**
         * @var string
         */
        public $imageHTML;
        /**
         * @var array|bool
         */
        public $profile_photo;
        /**
         * @var \WP_Post
         */
        private $post;

        /**
         * Person constructor.
         *
         * @param \WP_Post $person
         */
        public function __construct(\WP_Post $person)
        {
            $this->post = $person;
            $this->adult_name = get_the_title($person);
            $this->school_name = $this->getMeta('full_title');
            $this->profile_photo = $this->getImages();
            $this->permalink = get_permalink($person);
            $this->biography = $this->getBiography();
            $this->jobtitle = $this->getMeta('leadjobtitle');
            $this->imageHTML = $this->getPhotoImageHTML();
            $this->post_id = get_the_ID($person);
            $this->post_slug = $person->post_name;
        }

        /**
         * @param string $fieldID
         *
         * @return mixed
         */
        private function getMeta(string $fieldID)
        {
            return get_post_meta($this->post->ID, Metaboxes::fieldID($fieldID), true);
        }

        /**
         * @return array|bool
         */
        private function getImages()
        {
            if (has_post_thumbnail($this->post->ID)) {
                $photos = [];
                foreach ($this->imageSizes() as $size) {
                    $photos[$size] = wp_get_attachment_image_src(get_post_thumbnail_id($this->post->ID), $size);
                }

                return $photos;
            }

            return false;
        }

        /**
         * @return array
         */
        private function imageSizes(): array
        {
            return [
                'thumbnail',
                Plugin::PROFILE_PHOTO_SIZE_NAME,
                'full',
            ];
        }

        /**
         * @return string
         */
        private function getBiography(): string
        {
            $content = get_the_content($this->post);
            if ($content) {
                return $content;
            }

            return '';
        }

        /**
         * @return string
         */
        private function getPhotoImageHTML(): string
        {
            return wp_get_attachment_image(get_post_thumbnail_id($this->post->ID), Plugin::PROFILE_PHOTO_SIZE_NAME);
        }
    }
