<?php
namespace CranleighSchool\CranleighPeople;

use WP_Query;
use WP_Error;
use stdClass;

class RestAPI {

	public function __construct() {
		add_action( 'rest_api_init', array($this,'wpshout_register_routes') );
		add_action( 'rest_api_init', array($this, 'listStaff'));
		add_filter( 'rest_query_vars', array($this,'my_allow_meta_query' ));
	}

	public function my_allow_meta_query( $valid_vars ) {

		$valid_vars = array_merge( $valid_vars, array( 'meta_key', 'meta_value' ) );
		return $valid_vars;
	}

	public function listStaff() {
		$args = [
			"posts_per_page" => -1,
			"post_type" => "staff"
		];

		if (isset($_GET['username'])) {
			$args["meta_query"] = [[
				"key" => "staff_username",
				"value" => $_GET['username']
			]];
		}
		$query = new WP_Query($args);
		$output = [];

		while($query->have_posts()): $query->the_post();
			$person = new stdClass();
			$person->username = get_post_meta(get_the_ID(), 'staff_username', true);
			$photoAttachmentID = get_post_thumbnail_id();
			$person->imageHTML = wp_get_attachment_image($photoAttachmentID, "staff-profile");
			$person->name = get_the_title();
			$person->jobTitle = get_post_meta(get_the_ID(), 'staff_leadjobtitle',true);
			$person->ID = get_the_ID();
			$output[$person->username] = $person;
		endwhile;
		wp_reset_query();
		wp_reset_postdata();

		if ($query->post_count !== 1) {
			return $output;
		} else {
			return $person;
		}
	}
	public function wpshout_register_routes() {
	    register_rest_route(
	        'people',
	        'photos',
	        array(
	            'methods' => 'GET',
	            'callback' => array($this,'photos'),
	        )
	    );

		register_rest_route(
			'people',
			'list',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'listStaff')
			)
		);

		register_rest_route(
			'people',
			'/staff/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'listStaff')
			)
		);
	}
	public function photos() {
		global $wpdb;

		$people = $wpdb->get_results(
			"SELECT
				wp.ID as post_id,
				wp.post_title,
				wm.meta_value as username,
				sss.isams_id
			FROM `".$wpdb->prefix."posts` wp
			INNER JOIN `".$wpdb->prefix."postmeta` wm ON (wm.`post_id` = wp.`ID` AND wm.`meta_key`='staff_username')
			INNER JOIN `senior_staff_sync` sss on (sss.`username` = wm.`meta_value`)
			WHERE `post_type`='staff' AND `post_status`='publish'
			ORDER BY wp.post_date ASC");

		$output = array();

		foreach ($people as $person):

			$image = get_the_post_thumbnail_url( $person->post_id, 'full' );
			$url = (parse_url($image, PHP_URL_PATH));
			$ext = pathinfo($url)['extension'];
			$array = [
				"isams_id" => $person->isams_id,
				"username" => $person->username,
				"image" => $image,
				"name" => $person->post_title,
				"newfilename" => $person->isams_id.".".$ext,
			];

			if ($image !== FALSE)
				array_push($output, $array);
		endforeach;

		return array(
			'num' => count($output),
			"result" => $output
		);
	}
}
