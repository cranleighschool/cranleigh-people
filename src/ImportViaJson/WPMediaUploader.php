<?php

namespace CranleighSchool\CranleighPeople\ImportViaJson;

use WP_Post;

class WPMediaUploader
{
    use SaveMetaTrait;

    private int $featured_image_id;

    public function __construct(
        private readonly PeopleManagerPhoto $photo,
        private readonly WP_Post            $post,
        private readonly string             $persons_name,
        private ?string                     $image_description = null
    )
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
        // TODO: This is a bit of a mess. It should be refactored to be more like the other classes.
        $upload = wp_upload_bits($this->photo->filename, null, $this->photo->binary);

        $filename = $upload['file'];
        $wp_filetype = wp_check_filetype($filename, null);

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => $this->persons_name,
            'post_name' => sanitize_title($this->persons_name) . '-portrait-photo',
            'post_content' => $this->image_description,
            'post_status' => 'inherit',
        );

        $this->featured_image_id = wp_insert_attachment($attachment, $filename, $this->post->ID);
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
        set_post_thumbnail($this->post->ID, $this->featured_image_id);
        $this->saveMeta('featured_image_set_time', date_i18n('Y-m-d H:i:s'));
    }
}
