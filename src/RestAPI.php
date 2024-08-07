<?php

namespace CranleighSchool\CranleighPeople;

use CranleighSchool\CranleighPeople\Api\Person;
use stdClass;
use WP_Error;
use WP_Query;

class RestAPI {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'wpshout_register_routes' ) );
		add_action( 'rest_api_init', array( $this, 'listStaff' ) );
		add_filter( 'rest_query_vars', array( $this, 'my_allow_meta_query' ) );
	}

	public function defaultArgs() {
		return array(
			'posts_per_page' => -1,
			'post_type'      => Plugin::POST_TYPE_KEY,
		);
	}

	public function my_allow_meta_query( array $valid_vars ) {
		$valid_vars = array_merge( $valid_vars, array( 'meta_key', 'meta_value' ) );

		return $valid_vars;
	}

	public function personOutput( \WP_REST_Request $request ) {
		global $post;
		$args = array();

		if ( $request->get_param( 'username' ) ) {
			$args['meta_query'] = array(
				array(
					'key'   => Metaboxes::fieldID( 'username' ),
					'value' => $request->get_param( 'username' ),
				),
			);
		}

		$query = new WP_Query( wp_parse_args( $args, $this->defaultArgs() ) );

		if ( $query->have_posts() ) {
			$output = array();

			while ( $query->have_posts() ) {
				$query->the_post();
				$output[] = new Person( $post );
			}
			wp_reset_query();
			wp_reset_postdata();

			if ( count( $output ) == 1 ) {
				// If there is only one result, just output that, not in an array
				return $output[0];
			}

			return $output;
		} else {
			// No results
			return new WP_Error( 'no_staff', 'No Staff Member(s) Found', array( 'status' => 404 ) );
		}

		return false;
	}

	public function listStaff() {
		$args = array(
			'posts_per_page' => -1,
			'post_type'      => Plugin::POST_TYPE_KEY,
		);

		if ( isset( $_GET['username'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'   => Metaboxes::fieldID( 'username' ),
					'value' => $_GET['username'],
				),
			);
		}
		$query = new WP_Query( $args );
		$output = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$person = new stdClass();
			$person->username = get_post_meta( get_the_ID(), Metaboxes::fieldID( 'username' ), true );
			$photoAttachmentID = get_post_thumbnail_id();
			$person->imageHTML = wp_get_attachment_image( $photoAttachmentID, Plugin::PROFILE_PHOTO_SIZE_NAME );
			$person->name = get_the_title();
			$person->jobTitle = get_post_meta( get_the_ID(), Metaboxes::fieldID( 'leadjobtitle' ), true );
			$person->ID = get_the_ID();
			$output[ $person->username ] = $person;
		}
		wp_reset_query();
		wp_reset_postdata();

		if ( $query->post_count !== 1 ) {
			return $output;
		} else {
			return $person;
		}
	}

	public function append_custom_meta( $object, $field_name, $request ) {
		return new Person( get_post( $object['id'] ) );
	}

	public function wpshout_register_routes() {
		register_rest_field(
			'staff',
			'custom_meta',
			array(
				'get_callback'    => array( $this, 'append_custom_meta' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);

		register_rest_route(
			'people',
			'photos',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'photos' ),
                'permission_callback' => '__return_true'
            )
		);
		register_rest_route(
			'people',
			'staff/(?P<username>\w+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'personOutput' ),
                'permission_callback' => '__return_true'
			)
		);
		register_rest_route(
			'people',
			'staff',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'personOutput' ),
                'permission_callback' => '__return_true'

            )
		);
		register_rest_route(
			'people',
			'list',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'listStaff' ),
                'permission_callback' => '__return_true'

            )
		);
	}

	public function photos() {
		global $wpdb;

		$people = $wpdb->get_results(
			'SELECT
				wp.ID as post_id,
				wp.post_title,
				wm.meta_value as username,
				sss.isams_id
			FROM `' . $wpdb->prefix . 'posts` wp
			INNER JOIN `' . $wpdb->prefix . "postmeta` wm ON (wm.`post_id` = wp.`ID` AND wm.`meta_key`='" . Metaboxes::fieldID( 'username' ) . "')
			INNER JOIN `senior_staff_sync` sss on (sss.`username` = wm.`meta_value`)
			WHERE `post_type`='" . Plugin::POST_TYPE_KEY . "' AND `post_status`='publish'
			ORDER BY wp.post_date ASC"
		);

		$output = array();

		foreach ( $people as $person ) {
			$image = get_the_post_thumbnail_url( $person->post_id, 'full' );
			$url = ( parse_url( $image, PHP_URL_PATH ) );
			if ( isset( pathinfo( $url )['extension'] ) ) {
				$ext = pathinfo( $url )['extension'];
			} else {
				$ext = '.unknowon';
			}
			$array = array(
				'isams_id'    => $person->isams_id,
				'username'    => $person->username,
				'image'       => $image,
				'name'        => $person->post_title,
				'newfilename' => $person->isams_id . '.' . $ext,
			);

			if ( $image !== false ) {
				array_push( $output, $array );
			}
		}

		return array(
			'num'    => count( $output ),
			'result' => $output,
		);
	}
}
