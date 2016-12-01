<?php
/*
	Plugin Name: Cranleigh People
	Plugin URI: http://www.cranleigh.org
	Description: One plugin that controls the people who work at Cranleigh.
	Author: Fred Bradley
	Version: 1.2.0
	Author URI: http://fred.im
*/

require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
require_once(dirname(__FILE__).'/settingsapiwrapper.php');
require_once(dirname(__FILE__).'/settings.php');
require_once(dirname(__FILE__).'/widget.php');
require_once(dirname(__FILE__).'/shortcode.php');

class cran_peeps {
	public $post_type_key = "staff";
	private $card_types = false;


	/**
	 * __construct function. Contains all the actions and filters for the class.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		$this->card_types = (object) [
		["value" => "hod", "title" => "Head of Department"],
		["value" => "house", "title" => "House"],
		["value" => "small", "title" => "Small"],
		["value" => "two-column", "title" => "Two Column"]
	];
		$settings = get_option('cran_people_basic');
		if (isset($settings['load_cpt']) && $settings['load_cpt']=="yes"):
			register_activation_hook(__FILE__, array($this, 'activate'));

			add_action( 'init', array($this, 'CPT_Cranleigh_People'));
			add_action( 'init', array($this, 'TAX_staff_cats'));

			add_action("after_setup_theme", array($this, 'profile_pictures'));
			add_action("plugins_loaded", array($this, 'is_meta_box_alive'));

			add_filter( 'rwmb_meta_boxes', array($this, 'meta_boxes'));

			add_action( 'admin_menu' , array($this, 'remove_staff_cats_tax_box'));
			add_action( 'tgmpa_register', array($this, 'cs__register_required_plugins') );

			add_shortcode("cranleigh-person", array($this, 'person_shortcode'));
			add_filter("enter_title_here", array($this, 'title_text_input'));

			add_filter('manage_posts_columns', array($this,'add_photo_column_to_listing'));
			add_action('manage_posts_custom_column', array($this,'add_photo_to_listing'), 10, 2);

			add_action( 'pre_get_posts', array($this,'owd_post_order') );


			add_action('media_buttons', array($this, 'add_media_button'), 900);
			add_action('wp_enqueue_media', array($this, 'include_media_button_js_file'));
			add_action( 'admin_print_footer_scripts', array( $this, 'add_mce_popup' ) );

		endif;
	}

	function is_meta_box_alive() {
		if (defined('RWMB_VER')):
			return true;
		else:
			return false;
		endif;

	}

	/**
	 * activate function. Called only once upon activation of the plugin on any site.
	 *
	 * @access public
	 * @return void
	 */

	function activate() {
		$this->insert_staff_roles();
	}


	/**
	 * remove_staff_cats_tax_box function.
	 *
	 * @access public
	 * @return void
	 */
	function remove_staff_cats_tax_box() {
		remove_meta_box( 'staff_categoriesdiv' , 'staff' , 'side' );
	}

	/**
	 * profile_pictures function.
	 *
	 * @access public
	 * @return void
	 */
	function profile_pictures() {
		add_image_size("staff-photo", 500, 500, true);
	}




	/**
	 * title_text_input function.
	 *
	 * @access public
	 * @param mixed $title
	 * @return void
	 */
	function title_text_input($title) {
		if (get_post_type()==$this->post_type_key):
			return $title = '(first name) (surname)';
		endif;
		return $title;
	}

	/**
	 * meta_boxes function.
	 * Uses the 'rwmb_meta_boxes' filter to add custom meta boxes to our custom post type.
	 * Requires the plugin "meta-box"
	 *
	 * @access public
	 * @param array $meta_boxes
	 * @return void
	 */
	function meta_boxes($meta_boxes) {
		$prefix = "staff_";
		$meta_boxes[] = array(
			"id" => "staff_meta_side",
			"title" => "Staff Info",
			"post_types" => array($this->post_type_key),
			"context" => "side",
			"priority" => "high",
			"autosave" => true,
			"fields" => array(
				array(
					"name" => __("Surname", "cranleigh"),
					"id" => "{$prefix}surname",
					"type" => "text",
					"desc" => "for sorting purposes only",
					'disabled'  => true,
				),
				array(
					"name" => __("Cranleigh Username", "cranleigh"),
					"id" => "{$prefix}username",
					"type" => "text",
					"desc" => "eg. Dave Futcher is &quot;DJF&quot;"
				),
				array(
					"name" => __("Position(s)", "text_domain"),
					"id" => "{$prefix}position",
					"type" => "text",
					"clone" => true,
//					'options'     => $this->staff_roles(),
					"desc" => "Start typing a role. If the role you're after doesn't exist then add it <a href='edit-tags.php?taxonomy=staff_categories&post_type=staff' target='_blank'>here</a>. If you have more than one role, add them all here."
				),
				array(
					"name" => __("Lead Job Title", "cranleigh"),
					"id" => "{$prefix}leadjobtitle",
					"type" => "text",
					"desc" => "The job title that will show on on your cards, and contacts"
				),
				array(
					"name" => __("Full Title", "cranleigh"),
					"id" => "{$prefix}full_title",
					"type" => "text",
					"desc" => "eg. Mr Charlie H.D. Boddington BA, PGCE"
				),
				array(
					"name" => __("Email Address", "cranleigh"),
					"id" => "{$prefix}email_address",
					"type" => "email",
					"desc" => "djf@cranleigh.org"
				),
				array(
					"name" => __("Phone Number", "cranleigh"),
					"id" => "{$prefix}phone",
					"type" => "text",
					"desc" => "01483 542019"
				)
			),
			'validation' => array(
				'rules'    => array(
					"{$prefix}position" => array(
						'required'  => true,
						'minlength' => 3,
					),
					"{$prefix}leadjobtitle" => array(
						"required" => true,
						"minlength" => 3
					)
				),
				// optional override of default jquery.validate messages
				'messages' => array(
					"{$prefix}position" => array(
						'required'  => __( 'Position is required', 'text_domain' ),
						'minlength' => __( 'Position must be at least 3 characters', 'text_domain' ),
					),
					"{$prefix}leadjobtitle" => array(
						"required" => __("You must enter a lead job title", "cranleigh"),
						"minlength" => __("Job Title must be at least 3 characters", "cranleigh")
					)
				),
			),
		);

/*		$meta_boxes[] = array(
			"id" => "staff_meta_normal",
			"title" => "Staff Meta",
			"post_types" => array($this->post_type_key),
			"context" => "normal",
			"autosave" => true,
			"fields" => array(
				array(
					"name" => __("Excerpt")
				)
			)
		)*/
		return $meta_boxes;
	}

	// Register Custom Post Type
	function CPT_Cranleigh_People() {

		$labels = array(
			'name'                  => _x( 'Cranleigh People', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Person', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'People', 'text_domain' ),
			'name_admin_bar'        => __( 'People', 'text_domain' ),
			'archives'              => __( 'Cranleigh People', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
			'all_items'             => __( 'All Cranleigh People', 'text_domain' ),
			'add_new_item'          => __( 'Add New Person', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'new_item'              => __( 'New Person', 'text_domain' ),
			'edit_item'             => __( 'Edit Person', 'text_domain' ),
			'update_item'           => __( 'Update Person', 'text_domain' ),
			'view_item'             => __( 'View Person', 'text_domain' ),
			'search_items'          => __( 'Search Person', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Profile Picture', 'text_domain' ),
			'set_featured_image'    => __( 'Select Profile Picture', 'text_domain' ),
			'remove_featured_image' => __( 'Remove Profile Picture', 'text_domain' ),
			'use_featured_image'    => __( 'Use as Profile Picture', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
			'items_list'            => __( 'People list', 'text_domain' ),
			'items_list_navigation' => __( 'People list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter people list', 'text_domain' ),
		);
		$args = array(
			'label'                 => __( 'Person', 'text_domain' ),
			'description'           => __( 'A List of People that are mentioned on the website', 'text_domain' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail','excerpt' ),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-businessman',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( $this->post_type_key, $args );

	}

	// Register Custom Taxonomy
	function TAX_staff_cats() {

		$labels = array(
			'name'                       => _x( 'Staff Groups', 'Taxonomy General Name', 'text_domain' ),
			'singular_name'              => _x( 'Staff Group', 'Taxonomy Singular Name', 'text_domain' ),
			'menu_name'                  => __( 'Staff Groups', 'text_domain' ),
			'all_items'                  => __( 'All Staff Groups', 'text_domain' ),
			'parent_item'                => __( 'Parent Group', 'text_domain' ),
			'parent_item_colon'          => __( 'Parent Group:', 'text_domain' ),
			'new_item_name'              => __( 'New Group Name', 'text_domain' ),
			'add_new_item'               => __( 'Add New Group', 'text_domain' ),
			'edit_item'                  => __( 'Edit Group', 'text_domain' ),
			'update_item'                => __( 'Update Group', 'text_domain' ),
			'view_item'                  => __( 'View Group', 'text_domain' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
			'add_or_remove_items'        => __( 'Add or remove groups', 'text_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
			'popular_items'              => __( 'Popular Groups', 'text_domain' ),
			'search_items'               => __( 'Search Groups', 'text_domain' ),
			'not_found'                  => __( 'Not Found', 'text_domain' ),
			'no_terms'                   => __( 'No items', 'text_domain' ),
			'items_list'                 => __( 'Items list', 'text_domain' ),
			'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
		);
		register_taxonomy( 'staff_categories', array( 'staff' ), $args );

	}
	function insert_staff_roles() {
		$this->TAX_staff_cats();
		$args = array();

		$roles = array(
			"Head of Department",
			"Deputy Head",
			"Headmaster",
			"Housemaster / Housemistress",
			"Teacher",
			"Senior Management Team"
		);

		foreach ($roles as $role):
			$test[] = wp_insert_term($role, "staff_categories");
		endforeach;
	}

	function staff_roles() {
		$args = array(
			"hide_empty" => false
		);
		$terms = get_terms("staff_categories", $args);
		$output = array();
		foreach($terms as $role) {
			$output[$role->slug] = $role->name;
		}
		return $output;
	}

	function person_shortcode($atts, $content=null) {
		global $post;
		$a = shortcode_atts(array(
			"id" => null,
			"slug" => null
		), $atts);

		$query_args = array(
			"post_type" => $this->post_type_key,
			"posts_per_page" => 1
		);

		if ($a['id']) {
			$query_args['p'] = $a['id'];
		} elseif ($a['slug']) {
			$query_args['post_name'] = $a['slug'];
		} else {
			return "Incorrect Data Given";
		}
		$output = "";
		$query = new WP_Query($query_args);
		if ($query->have_posts()):
			while($query->have_posts()): $query->the_post();
				$output .= get_the_post_thumbnail(get_the_ID(), 'staff-photo', array("class"=>"img-responsive"));
				$output .= "<pre>";
				$output .= print_r($post, true);
				$output .= "</pre>";
			endwhile;
		else:
			$output = "Person Not Found";
		endif;
		wp_reset_query();
		return $output;
	}

	/**
	 * Register the required plugins for this theme.
	 *
	 * In this example, we register five plugins:
	 * - one included with the TGMPA library
	 * - two from an external source, one from an arbitrary source, one from a GitHub repository
	 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
	 *
	 * The variables passed to the `tgmpa()` function should be:
	 * - an array of plugin arrays;
	 * - optionally a configuration array.
	 * If you are not changing anything in the configuration array, you can remove the array and remove the
	 * variable from the function call: `tgmpa( $plugins );`.
	 * In that case, the TGMPA default settings will be used.
	 *
	 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
	 */
	function cs__register_required_plugins() {
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(

			array(
				'name'      => 'Meta Box',
				'slug'      => 'meta-box',
				'required'  => true,
			),

		);

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = array(
			'id'           => 'cranleigh',				// Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',						// Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins',	// Menu slug.
			'parent_slug'  => 'plugins.php',            // Parent menu slug.
			'capability'   => 'manage_options',			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,						// Show admin notices or not.
			'dismissable'  => true,						// If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',						// If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,					// Automatically activate plugins after installation or not.
			'message'      => '',						// Message to output right before the plugins table.

		);

		tgmpa( $plugins, $config );
	}

	function get_staff_photo($post_ID) {
		$post_thumb_id = get_post_thumbnail_id($post_ID);
		if ($post_thumb_id) {
			return get_the_post_thumbnail( $post_ID, array(100,100) );
			$post_thumb_img = wp_get_attachment_image_src($post_thumb_id, array(100,100));
			return $post_thumb_img[0];
		}
	}

	function add_photo_column_to_listing($defaults) {
		if (get_post_type()==$this->post_type_key) {
			$columns = array();
			$columns['cb'] = $defaults['cb'];
			$columns['title'] = $defaults['title'];
			$columns['staff_username'] = "Username";
			$columns['staff_leadjobtitle'] = "Lead Job Title";
			$columns['taxonomy-staff_categories'] = $defaults['taxonomy-staff_categories'];
			$columns['date'] = $defaults['date'];
			$columns['staff_photo'] = "Photo";
			return $columns;
		}
		return $defaults;
	}
	function add_photo_to_listing($column_name, $post_ID) {
		if ($column_name == 'staff_photo') {
			$post_featured_image = $this->get_staff_photo($post_ID);
			if ($post_featured_image) {
				echo $post_featured_image;
			}
		}
		if ($column_name == 'staff_username') {
			echo strtoupper(get_post_meta( $post_ID, 'staff_username', true ));
		}
		if ($column_name == 'staff_leadjobtitle') {
			echo get_post_meta( $post_ID, 'staff_leadjobtitle', true );
		}
	}

	// Order custom post types alphabetically
	function owd_post_order( $query ) {
		if ( $query->is_post_type_archive($this->post_type_key) && $query->is_main_query() ) {

			$query->set( 'orderby', 'meta_value' );
			$query->set('meta_key', 'staff_surname');
			$query->set( 'order', 'ASC' );
		}
		return $query;
	}


	function add_media_button() {
		echo '<style>.wp-media-buttons .person_card_insert span.wp-media-buttons-icon:before {
			font:400 18px/1 dashicons;
			content:"\f110";
			} </style>';
		echo '<a href="#" class="button person_card_insert" id="add_person_shortcode"><span class="wp-media-buttons-icon"></span>' . esc_html__( 'Person Card', 'cranleigh' ) . '</a>';

	}

	function include_media_button_js_file() {
		wp_enqueue_script('cranleigh_people_media_button', plugins_url('popme.js', __FILE__), array('jquery'), time(), true);
	}

	function add_mce_popup() {
		?>
		<script>
			function CranleighPeopleInsertShortcode(){

				var user = jQuery("#user").val();
				var title = jQuery("#card_title").val();
				var type = jQuery("#card_type").val();

//				smugmug_url = smugmug_url.trim();
//				if (smugmug_url.substr(0,4) != "http") {
//					alert(<?php echo json_encode( __( 'Please enter a valid URL, ensuring it starts with https://', 'cranleigh' ) ); ?>);
//					return;
//				}
				window.send_to_editor("[person_card user=\"" + user + "\" type=\"" + type + "\" title=\"" + title + "\"]");
				return;

    		}
		</script>

		<div id="insert_cranleigh_person" style="display:none;">
			<div id="insert_cranleigh_person_wrapper" class="wrap">
				<div id="insert-cranleigh-person-container">
					<label>User</label><br />
					<?php
					$args = [
						"post_type" => $this->post_type_key,
						"posts_per_page" => -1,
						"meta_key" => "staff_surname",
						"orderby" => "meta_value",
						"order" => "ASC"
					];

					$newquery = new WP_Query($args);

					?>
					<select id="user">
						<option value="">--SELECT A STAFF MEMBER---</option>
						<?php while($newquery->have_posts()): $newquery->the_post();
							$username = get_post_meta(get_the_ID(), "staff_username", true);
						?>
						<option value="<?php echo $username; ?>"><?php echo get_the_title()." (".$username.")"; ?></option>
						<?php endwhile;
							wp_reset_postdata();
							wp_reset_query();
						?>
					</select>
					<br />
					<label>Card Type</label><br />
					<select id="card_type">
						<option value="">--SELECT A CARD TYPE---</option>
						<?php foreach($this->card_types as $card_type):
							 ?>
						<option value="<?php echo $card_type['value']; ?>"><?php echo $card_type['title']; ?></option>
						<?php endforeach; ?>
					</select>
					<br />
					<label>Card Title</label><br />
					<input type="text" id="card_title" style="padding:5px;width:100%;border-radius: 5px;" placeholder="Card Title" />

					<div style="padding-top:15px;">
						<input type="button" class="button-primary" value="Insert Shortcode" onclick="CranleighPeopleInsertShortcode();"/>
						<a class="button" href="#" onclick="tb_remove(); return false;">
							<?php _e("Cancel", "js_shortcode"); ?>
						</a>
        			</div>

				</div>
			</div>
		</div>

	<?php
	}

}

$cran_peeps_plugin = new cran_peeps();



