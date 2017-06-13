<?php
namespace CranleighPeople;

use WP_Query;
use WP_Error;

class RestAPI {

	public function __construct() {
		add_action( 'rest_api_init', array($this,'wpshout_register_routes') );
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
