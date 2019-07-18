<?php


	namespace CranleighSchool\CranleighPeople;


	/**
	 * Class Metaboxes
	 *
	 * @package CranleighSchool\CranleighPeople
	 */
	class Metaboxes
	{
		public const PREFIX = "staff_";

		/**
		 * Statically called method to register the class.
		 */
		public static function register(): void
		{
			$instance = new self;
			add_filter('rwmb_meta_boxes', [$instance, 'meta_boxes']);
		}

		/**
		 * @return bool
		 */
		private function should_surname_be_read_only(): bool
		{
			if (Plugin::getPluginSetting('isams_controlled') == 'yes') {
				return true;
			} else {
				return false;
			}
		}

		public static function fieldID(string $name): string
		{
			return self::PREFIX . $name;
		}

		/**
		 * meta_boxes function.
		 * Uses the 'rwmb_meta_boxes' filter to add custom meta boxes to our custom post type.
		 * Requires the plugin "meta-box"
		 *
		 * @access public
		 *
		 * @param array $meta_boxes
		 *
		 * @return array $meta_boxes
		 */
		public function meta_boxes(array $meta_boxes): array
		{
			$meta_boxes[] = [
				'id'         => 'staff_meta_side',
				'title'      => 'Staff Info',
				'post_types' => [Plugin::POST_TYPE_KEY],
				'context'    => 'side',
				'priority'   => 'high',
				'autosave'   => true,
				'fields'     => [
					[
						'name'     => __('Surname', 'cranleigh'),
						'id'       => self::fieldID("surname"),
						'type'     => 'text',
						'desc'     => 'Used for sorting purposes',
						'readonly' => $this->should_surname_be_read_only()
					],
					[
						'name' => __('Cranleigh Username', 'cranleigh'),
						'id'   => self::fieldID("username"),
						'type' => 'text',
						'desc' => 'eg. Dave Futcher is &quot;DJF&quot;',
					],
					[
						'name' => __('Lead Job Title', 'cranleigh'),
						'id'   => self::fieldID('leadjobtitle'),
						'type' => 'text',
						'desc' => 'The job title that will show on on your cards, and contacts',
					],
					[
						'name'  => __('More Position(s)', 'cranleigh-2016'),
						'id'    => self::fieldID('position'),
						'type'  => 'text',
						'clone' => true,
						'desc'  => '',
					],
					[
						'name' => __('Full Title', 'cranleigh'),
						'id'   => self::fieldID('full_title'),
						'type' => 'text',
						'desc' => 'eg. Mr Charlie H.D. Boddington. (This will be the title of the card)',
					],
					[
						'name' => __('Qualifications', 'cranleigh'),
						'id'   => self::fieldID('qualifications'),
						'type' => 'text',
						'desc' => 'eg. BA, DipEd, BEng, PhD',
					],
					[
						'name' => __('Email Address', 'cranleigh'),
						'id'   => self::fieldID('email_address'),
						'type' => 'email',
						'desc' => 'eg. djf@cranleigh.org',
					],
					[
						'name' => __('Phone Number', 'cranleigh'),
						'id'   => self::fieldID('phone'),
						'type' => 'text',
						'desc' => 'eg. 01483 542019',
					],
				],
				'validation' => $this->validation(),
			];

			return $meta_boxes;
		}

		/**
		 * @return array
		 */
		private function validation(): array
		{
			$rules = [
				self::fieldID('position')     => [
					'required'  => true,
					'minlength' => 3,
				],
				self::fieldID('leadjobtitle') => [
					'required'  => true,
					'minlength' => 3,
				],
				self::fieldID('username')     => [
					'required' => true,
				],
				self::fieldID('surname')      => [
					'required' => true,
				],
			];

			$messages = [
				self::fieldID('position')     => [
					'required'  => __('Position is required', 'cranleigh-2016'),
					'minlength' => __('Position must be at least 3 characters', 'cranleigh-2016'),
				],
				self::fieldID('leadjobtitle') => [
					'required'  => __('You must enter a lead job title', 'cranleigh'),
					'minlength' => __('Job Title must be at least 3 characters', 'cranleigh'),
				],
			];

			return [
				'rules'    => $rules,
				'messages' => $messages
			];
		}
	}
