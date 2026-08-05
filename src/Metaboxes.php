<?php

namespace CranleighSchool\CranleighPeople;

	/**
	 * Class Metaboxes.
	 */
class Metaboxes {

	public const PREFIX = 'staff_';

	/**
	 * Statically called method to register the class.
	 */
	public static function register(): void {
		$instance = new self();
		add_filter( 'rwmb_meta_boxes', array( $instance, 'meta_boxes' ) );
	}

	/**
	 * @return bool
	 */
	private function should_surname_be_read_only(): bool {
		if ( Plugin::getPluginSetting( 'isams_controlled' ) == 'yes' ) {
			return true;
		} else {
			return false;
		}
	}

	public static function fieldID( string $name ): string {
		return self::PREFIX . $name;
	}

	/**
	 * meta_boxes function.
	 * Uses the 'rwmb_meta_boxes' filter to add custom meta boxes to our custom post type.
	 * Requires the plugin "meta-box".
	 *
	 *
	 * @param array $meta_boxes
	 *
	 * @return array $meta_boxes
	 */
	public function meta_boxes( array $meta_boxes ): array {
		$meta_boxes[] = array(
			'id' => 'photo_updated_meta',
			'title' => 'Photo Meta',
			'post_types' => array( Plugin::POST_TYPE_KEY ),
			'context' => 'side',
			'priority' => 'low',
			'autosave' => true,
			'fields' => array(
				array(
					'name' => __( 'Mugshot Last Updated', 'cranleigh' ),
					'id' => self::fieldID( 'mugshot_updated_time' ),
					'type' => 'text',
					'desc' => 'The date that the photo on People Manager was updated...',
					'readonly' => true,
				),
				array(
					'name' => __( 'Featured Image Date Set', 'cranleigh' ),
					'id' => self::fieldID( 'featured_image_set_time' ),
					'type' => 'text',
					'desc' => 'The date that the featured image here was set...',
					'readonly' => true,
				),
			),
		);

		$meta_boxes[] = array(
			'id'         => 'staff_meta_side',
			'title'      => 'Staff Info',
			'post_types' => array( Plugin::POST_TYPE_KEY ),
			'context'    => 'side',
			'priority'   => 'high',
			'autosave'   => true,
			'fields'     => array(
				array(
					'name' => __('Prefix', 'cranleigh'),
					'id' => self::fieldID('prefix'),
					'type' => 'text',
					'desc' => 'eg. Mr, Mrs, Dr, Prof',
					'readonly' => $this->should_surname_be_read_only(),
				),
				array(
					'name' => __('Preferred Name', 'cranleigh'),
					'id' => self::fieldID('prename'),
					'type' => 'text',
					'desc' => 'eg. John, Matt, Dave, Gary',
					'readonly' => $this->should_surname_be_read_only(),
				),
				array(
					'name'     => __( 'Surname', 'cranleigh' ),
					'id'       => self::fieldID( 'surname' ),
					'type'     => 'text',
					'desc'     => 'Used for sorting purposes',
					'readonly' => $this->should_surname_be_read_only(),
				),
				array(
					'name' => __( 'Cranleigh Username', 'cranleigh' ),
					'id'   => self::fieldID( 'username' ),
					'type' => 'text',
					'desc' => 'eg. Dave Futcher is &quot;DJF&quot;',
				),
				array(
					'name' => __( 'Lead Job Title', 'cranleigh' ),
					'id'   => self::fieldID( 'leadjobtitle' ),
					'type' => 'text',
					'desc' => 'The job title that will show on on your cards, and contacts',
				),
				array(
					'name'  => __( 'More Position(s)', 'cranleigh-2016' ),
					'id'    => self::fieldID( 'position' ),
					'type'  => 'text',
					'clone' => true,
					'desc'  => '',
				),
				array(
					'name' => __( 'Full Title', 'cranleigh' ),
					'id'   => self::fieldID( 'full_title' ),
					'type' => 'text',
					'desc' => 'eg. Mr C H D Boddington. (This will be the title of the card)',
				),
				array(
					'name' => __( 'Qualifications', 'cranleigh' ),
					'id'   => self::fieldID( 'qualifications' ),
					'type' => 'text',
					'desc' => 'eg. BA, DipEd, BEng, PhD',
				),
				array(
					'name' => __( 'Email Address', 'cranleigh' ),
					'id'   => self::fieldID( 'email_address' ),
					'type' => 'email',
					'desc' => 'eg. djf@cranleigh.org',
				),
				array(
					'name' => __( 'Phone Number', 'cranleigh' ),
					'id'   => self::fieldID( 'phone' ),
					'type' => 'text',
					'desc' => 'eg. 01483 542019',
				),
			),
			'validation' => $this->validation(),
		);

		return $meta_boxes;
	}

	/**
	 * @return array
	 */
	private function validation(): array {
		$rules = array(
			self::fieldID( 'position' )     => array(
				'required'  => true,
				'minlength' => 3,
			),
			self::fieldID( 'leadjobtitle' ) => array(
				'required'  => true,
				'minlength' => 3,
			),
			self::fieldID( 'username' )     => array(
				'required' => true,
			),
			self::fieldID( 'surname' )      => array(
				'required' => true,
			),
		);

		$messages = array(
			self::fieldID( 'position' )     => array(
				'required'  => __( 'Position is required', 'cranleigh-2016' ),
				'minlength' => __( 'Position must be at least 3 characters', 'cranleigh-2016' ),
			),
			self::fieldID( 'leadjobtitle' ) => array(
				'required'  => __( 'You must enter a lead job title', 'cranleigh' ),
				'minlength' => __( 'Job Title must be at least 3 characters', 'cranleigh' ),
			),
		);

		return array(
			'rules'    => $rules,
			'messages' => $messages,
		);
	}
}
