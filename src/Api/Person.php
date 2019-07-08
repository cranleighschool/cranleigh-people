<?php

	namespace CranleighSchool\CranleighPeople\Api;

class Person {


	public $post_id;
	public $post_slug;
	public $adult_name;
	public $school_name;
	public $jobtitle;
	public $biography;
	public $permalink;
	public $imageHTML;
	public $profile_photo;
	private $post;

	public function __construct( \WP_Post $person ) {
		$this->post          = $person;
		$this->adult_name    = get_the_title( $person );
		$this->school_name   = $this->getMeta( 'staff_full_title' );
		$this->profile_photo = $this->getImages();
		$this->permalink     = get_permalink( $person );
		$this->biography     = $this->getBiography();
		$this->jobtitle      = $this->getMeta( 'staff_leadjobtitle' );
		$this->imageHTML     = $this->getPhotoImageHTML();
		$this->post_id       = get_the_ID( $person );
		$this->post_slug     = $person->post_name;

	}

	private function getMeta( $key ) {
		return get_post_meta( $this->post->ID, $key, true );
	}

	private function getImages() {
		if ( has_post_thumbnail( $this->post->ID ) ) {
			$photos = array();
			foreach ( $this->imageSizes() as $size ) {
				$photos[ $size ] = wp_get_attachment_image_src( get_post_thumbnail_id( $this->post->ID ), $size );
			}

			return $photos;
		}

		return false;
	}

	private function imageSizes() {
		 return [
			 'thumbnail',
			 'staff-photo',
			 'full',
		 ];
	}

	private function getBiography() {
		$content = get_the_content( $this->post );
		if ( $content ) {
			return $content;
		}

		return;

	}

	private function getPhotoImageHTML() {
		return wp_get_attachment_image( get_post_thumbnail_id( $this->post->ID ), 'staff-profile' );
	}
}
