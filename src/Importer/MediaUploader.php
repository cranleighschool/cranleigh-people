<?php

namespace CranleighSchool\CranleighPeople\Importer;

use CranleighSchool\CranleighPeople\Metaboxes;
use WP_Post;

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
     * MediaUploader constructor.
     *
     * @param string $url
     * @param int $parent_post_id
     * @param string $persons_name
     * @param string|null $image_description
     *
     * @throws \Exception
     */
    public function __construct(protected string $url, protected int $parent_post_id, protected string $persons_name, protected ?string $image_description = null)
    {

    }

    /**
     * @return WP_Post|false
     */
    public function upload(): WP_Post|false
    {
        if (wp_doing_ajax()) {
            return false;
        }
        $alt_text = 'Portrait Photo of ' . $this->persons_name;
        if ($this->image_description === null) {
            $this->image_description = $alt_text;
        }

        $checkImage = getimagesizefromstring($this->url);
        if ($checkImage === false) {
            $upload = wp_upload_bits(basename($this->url), null, file_get_contents($this->url));
        } else {
            $mime = $checkImage['mime'];
            $extension = str_replace('image/', '', $mime);
            $upload = wp_upload_bits($this->persons_name . '.' . $extension, null, $this->url);
        }

        $filename = $upload['file'];
        $wp_filetype = wp_check_filetype($filename, null);

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => $this->persons_name,
            'post_name' => sanitize_title($this->persons_name) . '-portrait-photo',
            'post_content' => $this->image_description,
            'post_status' => 'inherit',
        );

        $this->featured_image_id = wp_insert_attachment($attachment, $filename, $this->parent_post_id);
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($this->featured_image_id, $filename);
        wp_update_attachment_metadata($this->featured_image_id, $attach_data);
        $this->update_feature_image_to_post();
        wp_set_post_terms($this->featured_image_id, 'staff_profile_image');
        update_post_meta($this->featured_image_id, '_wp_attachment_image_alt', $alt_text, '');

        return get_post($this->featured_image_id);
    }

    private function update_feature_image_to_post(): void
    {
        set_post_thumbnail($this->parent_post_id, $this->featured_image_id);
        update_post_meta($this->parent_post_id, Metaboxes::fieldID('featured_image_set_time'), date_i18n('Y-m-d H:i:s'));
    }
}
