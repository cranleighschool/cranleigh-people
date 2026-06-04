<?php

namespace CranleighSchool\CranleighPeople;

use CranleighSchool\CranleighPeople\Api\Person;
use stdClass;
use WP_Error;
use WP_Query;

class RestAPI {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'wpshout_register_routes' ) );
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

		if ( ! $query->have_posts() ) {
			return new WP_Error( 'no_staff', 'No Staff Member(s) Found', array( 'status' => 404 ) );
		}

		$output = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$output[] = new Person( $post );
		}
		wp_reset_postdata();

		return count( $output ) === 1 ? $output[0] : $output;
	}

	public function listStaff( \WP_REST_Request $request ) {
		$args = array(
			'posts_per_page' => -1,
			'post_type'      => Plugin::POST_TYPE_KEY,
		);

		$username = $request->get_param( 'username' );
		if ( $username !== null ) {
			$args['meta_query'] = array(
				array(
					'key'   => Metaboxes::fieldID( 'username' ),
					'value' => sanitize_text_field( $username ),
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
		wp_reset_postdata();

		if ( count( $output ) === 1 ) {
			return reset( $output );
		}

		return $output;
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

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table names cannot use placeholders; values are plugin constants, not user input
		$people = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT
					wp.ID as post_id,
					wp.post_title,
					wm.meta_value as username,
					sss.isams_id
				FROM `' . $wpdb->prefix . 'posts` wp
				INNER JOIN `' . $wpdb->prefix . 'postmeta` wm ON (wm.`post_id` = wp.`ID` AND wm.`meta_key` = %s)
				INNER JOIN `senior_staff_sync` sss ON (sss.`username` = wm.`meta_value`)
				WHERE wp.`post_type` = %s AND wp.`post_status` = %s
				ORDER BY wp.post_date ASC',
				Metaboxes::fieldID( 'username' ),
				Plugin::POST_TYPE_KEY,
				'publish'
			)
		);

		$output = array();

		foreach ( $people as $person ) {
			$image = get_the_post_thumbnail_url( $person->post_id, 'full' );
			if ( $image === false ) {
				continue;
			}
			$pathinfo = pathinfo( parse_url( $image, PHP_URL_PATH ) );
			$ext = $pathinfo['extension'] ?? 'unknown';
			$output[] = array(
				'isams_id'    => $person->isams_id,
				'username'    => $person->username,
				'image'       => $image,
				'name'        => $person->post_title,
				'newfilename' => $person->isams_id . '.' . $ext,
			);
		}

		return array(
			'num'    => count( $output ),
			'result' => $output,
		);
	}
}
