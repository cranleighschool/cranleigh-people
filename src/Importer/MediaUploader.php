<?php

	namespace CranleighSchool\CranleighPeople\Importer;

	/**
	 * Class MediaUploader
	 *
	 * Example Usage:
	 * $image = new MediaUploader("http://domain.com/image.jpg", 4, "My Photo");
	 * $attachment = $image->upload();
	 *
	 * @package CranleighSchool\CranleighPeople\Importer
	 */
	class MediaUploader
	{
		/**
		 * @var int|null
		 */
		public $featured_image_id = NULL;
		/**
		 * @var string|null
		 */
		protected $url = NULL;
		/**
		 * @var int|null
		 */
		protected $parent_post_id = NULL;
		/**
		 * @var string|null
		 */
		protected $image_description = NULL;

		/**
		 * MediaUploader constructor.
		 *
		 * @param string $url
		 */
		public function __construct(string $url, int $parent_post_id, string $image_description)
		{
			$this->url = $url;
			$this->parent_post_id = $parent_post_id;
			$this->image_description = $image_description;
		}

		/**
		 * @return \WP_Post
		 * @throws \Exception
		 */
		public function upload(): \WP_Post
		{
			if (is_null($this->url) || is_null($this->parent_post_id) || is_null($this->image_description)) {
				throw new \Exception("Uploader class is lacking some detail", 400);
			}

			// Using this process as defined here: http://wordpress.stackexchange.com/questions/100838/how-to-set-featured-image-to-custom-post-from-outside-programmatically

			require_once(ABSPATH . 'wp-admin/includes/media.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/image.php');

			// magic sideload image returns an HTML image, not an ID
			$media = media_sideload_image($this->url, $this->parent_post_id, $this->image_description);

			if (is_wp_error($media)):
				error_log("There was a WP Error when trying to add image from: " . $this->url);
			endif;

			// therefore we must find it so we can set it as featured ID
			if (!empty($media) && !is_wp_error($media)) {
				$args = [
					'post_type'      => 'attachment',
					'posts_per_page' => -1,
					'post_status'    => 'any',
					'post_parent'    => $this->parent_post_id
				];

				// reference new image to set as featured
				$attachments = get_posts($args);

				if (isset($attachments) && is_array($attachments)) {
					foreach ($attachments as $attachment) {
						// grab source of full size images (so no 300x150 nonsense in path)
						$image = wp_get_attachment_image_src($attachment->ID, 'full');
						// determine if in the $media image we created, the string of the URL exists
						if (strpos($media, $image[0]) !== false) {
							// if so, we found our image. set it as thumbnail
							set_post_thumbnail($this->parent_post_id, $attachment->ID);
							//wp_set_post_terms($attachment->ID, $username, "post_tag");
							wp_set_post_terms($attachment->ID, "staff_profile_image", "category");
							update_post_meta($attachment->ID, "_wp_attachment_image_alt", $this->image_description, "");

							$this->featured_image_id = $attachment->ID;
							// only want one image
							break;
						}
					}
				}
			}

			return get_post($this->featured_image_id);

		}
	}
