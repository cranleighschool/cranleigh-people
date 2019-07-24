<?php

	namespace CranleighSchool\CranleighPeople\Shortcodes;

	use CranleighSchool\CranleighPeople\Exceptions\TooManyStaffFound;
	use CranleighSchool\CranleighPeople\Helper;
	use CranleighSchool\CranleighPeople\Metaboxes;
	use CranleighSchool\CranleighPeople\Plugin;
	use CranleighSchool\CranleighPeople\View;
	use WP_Query;

	trait ShortcodeTrait
	{
		/**
		 * @return string
		 */
		public static function get_first_paragraph(int $post_id = NULL): string
		{
			$str = wpautop(get_the_content(NULL, false, $post_id));
			$str = substr($str, 0, strpos($str, '</p>') + 4);
			$str = strip_tags($str, '<a><strong><em>');

			if (strlen(self::get_second_paragraph($post_id)) <= 1 && strlen($str) > 400) :
				return '<p class="biography">' . substr($str, 0, 400) . '...</p>';
			else :
				return '<p class="biography">' . $str . '</p>';
			endif;
		}

		/**
		 * @return string
		 */
		public static function get_second_paragraph(int $post_id = NULL): string
		{
			$str = wpautop(get_the_content(NULL, false, $post_id));
			$str = substr($str, strpos($str, '</p>') + 4);
			$str = strip_tags($str, '<p><a><strong><em>');

			return $str;
		}

		/**
		 * @param $card_title
		 *
		 * @return string|string[]|null
		 */
		public static function sanitize_title_to_id($card_title)
		{
			$string = strtolower(str_replace(' ', '', $card_title));

			return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
		}

		/**
		 * @param string $username
		 *
		 * @return \WP_Post
		 * @throws \CranleighSchool\CranleighPeople\Exceptions\TooManyStaffFound
		 */
		public static function get_wp_post_from_username(string $username): \WP_Post
		{
			$staff = self::get_wp_query_from_usernames([$username]);
			wp_reset_postdata();
			wp_reset_query();

			if ($staff->found_posts !== 1) {
				throw new TooManyStaffFound("Too Many Staff found", 400);
			}

			return $staff->posts[0];
		}

		/**
		 * @param \WP_Post $staff_member
		 *
		 * @return string
		 */
		public static function get_formatted_full_title(\WP_Post $staff_member): string
		{
			return sprintf(
				'<a href="%s"><span class="staff-title">%s</span></a><span class="qualifications">%s</span>',
				get_permalink($staff_member),
				get_post_meta($staff_member->ID, Metaboxes::fieldID('full_title'), true),
				get_post_meta($staff_member->ID, Metaboxes::fieldID('qualifications'), true)
			);
		}

		/**
		 * @param array $usernames
		 *
		 * @return \WP_Query
		 */
		public static function get_wp_query_from_usernames(array $usernames): WP_Query
		{
			$args = [
				'post_type'      => Plugin::POST_TYPE_KEY,
				'posts_per_page' => -1,
				'orderby'        => 'meta_value',
				'meta_key'       => Metaboxes::fieldID('surname'),
				'order'          => 'ASC',
				'meta_query'     => [
					[
						'key'     => Metaboxes::fieldID('username'),
						'value'   => $usernames,
						'compare' => 'IN',
					],
				],
			];

			return new WP_Query($args);
		}

		/**
		 * @param int $post_id
		 * @param string deprecated $card_title
		 *
		 * @return string
		 * @throws \Exception
		 */
		public static function small(int $post_id, string $card_title = NULL): string
		{
			$full_title = get_post_meta($post_id, Metaboxes::fieldID('full_title'), true);
			$position = get_post_meta($post_id, Metaboxes::fieldID('leadjobtitle'), true);

			return View::render('small-card', compact('full_title', 'position'));
		}

		/**
		 * @param string $number
		 *
		 * @return bool|string|string[]|null
		 * @throws \Exception
		 */
		public function phone_href(string $number)
		{
			return Helper::santitizePhoneHref($number);
		}

		/**
		 * @param      $positions
		 * @param null $not
		 *
		 * @return bool|mixed
		 */
		public function get_position($positions, $not = NULL)
		{

			return Helper::santitizePositions($positions, $not);
		}

		/**
		 * @param bool $thumb
		 *
		 * @deprecated Use View::the_post_thumbnail() instead.
		 *
		 */
		public function get_staff_photo(bool $thumb = false)
		{
			if (has_post_thumbnail()) :
				if ($thumb === false) :
					the_post_thumbnail(Plugin::PROFILE_PHOTO_SIZE_NAME, ['class' => 'img-responsive']);
				else :
					the_post_thumbnail('thumbnail', ['class' => 'img-responsive']);
				endif;
			else :
				$photo = wp_get_attachment_image(
					$this->default_attachment_id,
					Plugin::PROFILE_PHOTO_SIZE_NAME,
					false,
					['class' => 'img-responsive']
				);
				echo $photo;
			endif;
		}

		/**
		 * @return string
		 */
		public function default_card(): string
		{

			return 'Not Written Yet';
		}

		/**
		 * @param string $heading
		 * @param string $title
		 *
		 * @return string
		 */
		public function card_title(string $heading, string $title): string
		{

			return '<' . $heading . '>' . $title . '</' . $heading . '>';
		}

		/**
		 * @param int $post_id
		 *
		 * @return string
		 */
		public function two_column(int $post_id): string
		{
			$first_column = get_post_meta($post_id, Metaboxes::fieldID('full_title'), true);
			$last_column = get_post_meta($post_id, Metaboxes::fieldID('leadjobtitle'), true);

			return View::render('two-column', compact('first_column', 'last_column'));
		}
	}
