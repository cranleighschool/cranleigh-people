<?php

namespace CranleighSchool\CranleighPeople\Importer;

    use CranleighSchool\CranleighPeople\Metaboxes;

    /**
     * Class MediaUploader.
     *
     * Example Usage:
     * $image = new MediaUploader("http://domain.com/image.jpg", 4, "Person Name");
     * $attachment = $image->upload();
     */
    class MediaUploader
    {
        /**
         * @var int|null
         */
        public $featured_image_id = null;
        /**
         * @var string|null
         */
        protected $url = null;
        /**
         * @var int|null
         */
        protected $parent_post_id = null;
        /**
         * @var string|null
         */
        protected $image_description = null;

        /**
         * @var string|null
         */
        protected $persons_name = null;

        /**
         * MediaUploader constructor.
         *
         * @param string      $url
         * @param int         $parent_post_id
         * @param string      $persons_name
         * @param string|null $image_description
         *
         * @throws \Exception
         */
        public function __construct(string $url, int $parent_post_id, string $persons_name, string $image_description = null)
        {
            $this->url = $url;
            $this->parent_post_id = $parent_post_id;
            $this->image_description = $image_description;
            $this->persons_name = $persons_name;

            if (is_null($this->url) || is_null($this->parent_post_id) || is_null($this->persons_name)) {
                throw new \Exception('Uploader class is lacking some detail', 400);
            }
        }

        /**
         * @return \WP_Post
         */
        public function upload(): \WP_Post
        {
            if (wp_doing_ajax()) {
                return false;
            }
            $alt_text = 'Portrait Photo of '.$this->persons_name;
            if ($this->image_description === null) {
                $this->image_description = $alt_text;
            }
            $upload = wp_upload_bits(basename($this->url), null, file_get_contents($this->url));

            $filename = $upload['file'];
            $wp_filetype = wp_check_filetype($filename, null);

            $attachment = [
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => $this->persons_name,
                'post_name'      => sanitize_title($this->persons_name).'-portrait-photo',
                'post_content'   => $this->image_description,
                'post_status'    => 'inherit',
            ];

            $this->featured_image_id = wp_insert_attachment($attachment, $filename, $this->parent_post_id);
            require_once ABSPATH.'wp-admin/includes/image.php';
            $attach_data = wp_generate_attachment_metadata($this->featured_image_id, $filename);
            wp_update_attachment_metadata($this->featured_image_id, $attach_data);
            $this->update_feature_image_to_post();
            wp_set_post_terms($this->featured_image_id, 'staff_profile_image');
            update_post_meta($this->featured_image_id, '_wp_attachment_image_alt', $alt_text, '');

            return get_post($this->featured_image_id);
        }

        private function update_feature_image_to_post()
        {
            set_post_thumbnail($this->parent_post_id, $this->featured_image_id);
            update_post_meta($this->parent_post_id, Metaboxes::fieldID('featured_image_set_time'), date_i18n('Y-m-d H:i:s'));
        }
    }
