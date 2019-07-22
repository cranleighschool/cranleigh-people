<?php

	namespace CranleighSchool\CranleighPeople\Importer;


	use CranleighSchool\CranleighPeople\Exceptions\StaffNotFoundException;
	use CranleighSchool\CranleighPeople\Exceptions\TooManyStaffFound;
	use CranleighSchool\CranleighPeople\Metaboxes;
	use CranleighSchool\CranleighPeople\Plugin;
	use CranleighSchool\CranleighPeople\Slacker;

	/**
	 * Class Importer
	 *
	 * @package CranleighSchool\CranleighPeople\Importer
	 */
	class Importer
	{

		/**
		 * @param null $specific_people
		 *
		 * @return bool
		 * @throws \Exception
		 */
		public static function import($specific_people = NULL)
		{
			/** Annoyingly needed for WordPress callback parameters, only delivered as strings */
			if ($specific_people === '') {
				$specific_people = NULL;
			}

			$request = wp_remote_get(self::getSchoolApiEndPoint());
			if (is_wp_error($request)) {
				throw new \Exception("WP Error: " . $request->get_error_message());
			}


			$contentType = wp_remote_retrieve_header($request, 'content-type');

			if ($contentType !== "application/json") {
				// not what we are expecting, duck out early with a wp error and exception?
				throw new \Exception('Was expecting JSON, got ' . $contentType, 412);

				return false;
			}

			$body = wp_remote_retrieve_body($request);

			$result = json_decode($body);

			$i = 0;
			foreach ($result->data as $person) {
				$person = new Person($person);

				if ($specific_people !== NULL && !in_array($person->school_initials, $specific_people)) {
					continue;
				}
				try {
					$post_id = self::find_wp_staff_post($person->school_initials)->ID;
				} catch (TooManyStaffFound $exception) {
					continue;
				} catch (StaffNotFoundException $exception) {
					$post_id = 0;
				}


				self::updateOrCreate($person, $post_id);
				$i++;

			}
			self::slackmessage("Updated ".$i." People");


		}

		/**
		 * @return bool
		 */
		public static function getSchoolApiEndPoint()
		{
			return Plugin::getPluginSetting('importer_api_endpoint');
		}

		// TODO: Don't we need a method in here for checking the system status of a Person?

		/**
		 * @param string $school_initials
		 *
		 * @return \WP_Post|FALSE
		 * @throws \Exception
		 */
		public static function find_wp_staff_post(string $school_initials): \WP_Post
		{
			$args = [
				'post_type'      => Plugin::POST_TYPE_KEY,
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'meta_query'     => [
					[
						'key'     => Metaboxes::fieldID('username'),
						'value'   => $school_initials,
						'compare' => '=',
					]
				]
			];

			$posts = new \WP_Query($args);

			if ($posts->have_posts()) {
				if ($posts->found_posts > 1) {
					// too many posts
					throw new TooManyStaffFound('Too Many Staff Members Found matching ' . $school_initials, 400);
				}

			} else {
				// No Matching Post Found
				throw new StaffNotFoundException('No staff member found matching ' . $school_initials, 404);
			}

			while ($posts->have_posts()) : $posts->the_post();
				$staff_post = get_post(get_the_ID());
			endwhile;
			wp_reset_query();
			wp_reset_postdata();

			return $staff_post;

		}

		public static function slackmessage(string $message) {
			$slacker = new Slacker();
			$slacker->setRoom('it-cranleigh-people');
			$slacker->setUsername( 'Cranleigh People Importer' );
			$slacker->post( $message );

		}

		/**
		 * @param \CranleighSchool\CranleighPeople\Importer\Person $person
		 * @param int                                              $post_id
		 *
		 * @return array|\WP_Post|null
		 * @throws \Exception
		 */
		public static function updateOrCreate(Person $person, int $post_id = 0)
		{
			error_log("Start " . self::present_tense_verb($post_id) . " " . $person->prename_surname);



			$post_title = $person->prename_surname;
			$post_content = is_null($person->biography) ? '' : $person->biography;

			$staff_post = wp_insert_post([
				'post_type'    => Plugin::POST_TYPE_KEY,
				'post_status'  => 'publish', // TODO: Do we always want to publish?
				'ID'           => $post_id,
				'post_title'   => $post_title,
				'post_content' => $post_content
			]);

			if (is_wp_error($staff_post)) {
				throw new \Exception("Could not save post", 500);
			}
			$staff_post = get_post($staff_post);

			/**
			 * We only set the Username is the we are creating a new Staff.
			 */
			if ($post_id === 0) {
				self::saveMeta($staff_post, 'username', $person->school_initials);
			}

			self::saveMeta($staff_post, 'surname', $person->surname);
			self::saveMeta($staff_post, 'leadjobtitle', self::getLeadJobTitle($person->job_titles));
			self::saveMeta($staff_post, 'position', self::getOtherJobTitles($person->job_titles));
			self::saveMeta($staff_post, 'full_title', $person->label_salutation);
			self::saveMeta($staff_post, 'qualifications', self::qualificationsAsList($person->qualifications));
			self::saveMeta($staff_post, 'email_address', $person->email);
			self::saveMeta($staff_post, 'phone', $person->phone);

			// Do the Profile Pic
			$image = self::featureImageLogic($staff_post, $person);
			if ($image instanceof \WP_Post) {
				// Updated / Created Featured Image;
			} elseif ($image === true) {
				// Removed Image, because no image was on People Manager
			} elseif ($image === NULL) {
				// No logic was hit, changing nothing.
			} else {
				throw new \Exception('Error whilst checking featureImageLogic. Type: ' . gettype($image), 500);
			}

			return $staff_post;


		}

		/**
		 * Just a nice little helper function
		 *
		 * @param int $post_id
		 *
		 * @return string
		 */
		public static function present_tense_verb(int $post_id)
		{
			if ($post_id === 0) {
				return 'creating';
			} else {
				return 'updating';
			}
		}

		/**
		 * @param \WP_Post $staff_post
		 * @param string   $fieldName
		 * @param          $value
		 */
		public static function saveMeta(\WP_Post $staff_post, string $fieldName, $value)
		{

			$get = get_post_meta($staff_post->ID, Metaboxes::fieldID($fieldName), true);

			$done = update_post_meta($staff_post->ID, Metaboxes::fieldID($fieldName), $value);

		}

		/**
		 * @param array $jobTitles
		 *
		 * @return string|null
		 */
		public static function getLeadJobTitle(array $jobTitles)
		{
			if (isset($jobTitles[0])) {
				return $jobTitles[0];
			}

			return NULL;
		}

		/**
		 * @param array $jobTitles
		 *
		 * @return array
		 */
		public static function getOtherJobTitles(array $jobTitles): array
		{
			array_shift($jobTitles);

			return $jobTitles;

		}

		/**
		 * @param array $qualifications
		 *
		 * @return string
		 */
		public static function qualificationsAsList(array $qualifications): string
		{
			return implode(", ", $qualifications);
		}

		/**
		 * @param \WP_Post                                         $staff_post
		 * @param \CranleighSchool\CranleighPeople\Importer\Person $person
		 *
		 * @return bool|\WP_Post
		 * @throws \Exception
		 */
		public static function featureImageLogic(\WP_Post $staff_post, Person $person)
		{
			/**
			 * First, save the `photo_updated` property from People Manager into WordPress.
			 */
			self::saveMeta($staff_post, 'mugshot_updated_time', $person->photo_updated);

			/**
			 * If People Manager has no photo, but WordPress does, then remove the Featured Image.
			 */
			if ($person->photo_uri === NULL && has_post_thumbnail($staff_post)) {
				// TODO: Should we delete the Media Library item as well as remove the thumbnail meta link?
				return delete_post_thumbnail($staff_post); // True on success, false on failure
			}

			/**
			 * If People Manager has a photo, and WordPress does not, then give WordPress the photo.
			 */
			if ($person->photo_uri !== NULL && !has_post_thumbnail($staff_post)) {
				// Run importer
				return self::importImage($person->photo_uri, $staff_post);
			}

			/**
			 * Now we get nitty gritty.
			 *
			 * If People Manager has a photo AND WordPress has a photo,
			 * AND if the People Manager photo is newer than the Featured Image photo...
			 */
			if ($person->photo_uri !== NULL && has_post_thumbnail($staff_post) && self::api_photo_is_newer_than_wp_featured_image($person, $staff_post)) {
				// TODO: Should we delete the Media Library item as well as remove the thumbnail meta link?
				// Run Importer
				return self::importImage($person->photo_uri, $staff_post);
			}

		}

		/**
		 * @param string      $url
		 * @param \WP_Post    $parent_post
		 * @param string|NULL $image_description
		 *
		 * @return \WP_Post
		 * @throws \Exception
		 */
		public static function importImage(string $url, \WP_Post $parent_post, string $image_description = NULL): \WP_Post
		{
			$image = new MediaUploader($url, $parent_post->ID, $parent_post->post_title);

			return $image->upload();
		}

		/**
		 * @param \CranleighSchool\CranleighPeople\Importer\Person $api_person_object
		 * @param \WP_Post                                         $wp_post
		 *
		 * @return bool
		 */
		public static function api_photo_is_newer_than_wp_featured_image(Person $api_person_object, \WP_Post $wp_post): bool
		{

			if (strtotime($api_person_object->photo_updated) > strtotime(get_post_meta($wp_post->ID, Metaboxes::fieldID('featured_image_set_time'), true))) {
				return true;
			} else {
				return false;
			}
		}
	}
