<?php
namespace CranleighPeople;
use WP_Query;

class Plugin extends BaseController {
	public $post_type_key = "staff";
	private $card_types = false;


	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		parent::__construct();

		new Shortcode();

		$this->card_types = (object) [
			["value" => "hod", "title" => "Head of Department"],
			["value" => "house", "title" => "House"],
			["value" => "small", "title" => "Small"],
			["value" => "two-column", "title" => "Two Column"]
		];

		if ($this->settings['isams_controlled']=='yes') {
			$this->isams_controlled = true;
		} else {
			$this->isams_controlled = false;
		}

		if (isset($this->settings['load_cpt'])):

			if ($this->settings['load_cpt']=="yes"):

				$this->load_if_cpt();

			endif;

			$this->load_in_all_cases();
		else:

			add_action( 'admin_notices', array($this, 'notice_no_settings'));

		endif;

	}
	function load_in_all_cases() {
		add_action('media_buttons', array($this, 'add_media_button'), 900);
		add_action('wp_enqueue_media', array($this, 'include_media_button_js_file'));
		add_action( 'admin_print_footer_scripts', array( $this, 'add_mce_popup' ) );
		add_action('widgets_init', function() {
			// You can keep adding to this if you have added more class files
			// - just ensure that the name of the child class is what you put in as a registered widget.
			register_widget('CranleighPeople\Cranleigh_People_Widget');
		});

	}

	function load_if_cpt() {
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

		add_filter('manage_edit-staff_columns', array($this, 'add_photo_column_to_listing'));
		add_action('manage_posts_custom_column', array($this,'add_photo_to_listing'), 10, 2);

		add_action( 'pre_get_posts', array($this,'owd_post_order') );


		add_action('admin_notices', array($this, 'admin_notice'));
		add_action('admin_head', array($this, 'admin_head'));

		add_filter('tiny_mce_before_init', function($args) {
			global $pagenow;
			if ($pagenow=='post.php' && get_post_type()=='staff') {
				$args['readonly'] = true;
				$args['toolbar'] = false;
			}
			return $args;
		});

		if (get_post_type()=='staff'):
			add_filter('wp_default_editor', array($this, 'force_default_editor'));
		endif;
	}


	function notice_no_settings() {
		echo '<div class="notice notice-warning"><p><strong>Cranleigh People:</strong> You need to save your Cranleigh People Settings. Please <a href="'.menu_page_url( 'cranleigh_people_settings', false ).'">click here</a></p></div>';
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
		add_image_size("staff-photo", 400, 600, true);
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
					"desc" => "Used for sorting purposes",
					'readonly'  => $this->isams_controlled,
				),
				array(
					"name" => __("Cranleigh Username", "cranleigh"),
					"id" => "{$prefix}username",
					"type" => "text",
					"desc" => "eg. Dave Futcher is &quot;DJF&quot;"
				),
				array(
					"name" => __("Lead Job Title", "cranleigh"),
					"id" => "{$prefix}leadjobtitle",
					"type" => "text",
					"desc" => "The job title that will show on on your cards, and contacts"
				),
				array(
					"name" => __("More Position(s)", "text_domain"),
					"id" => "{$prefix}position",
					"type" => "text",
					"clone" => true,
					"desc" => ""
				),
				array(
					"name" => __("Full Title", "cranleigh"),
					"id" => "{$prefix}full_title",
					"type" => "text",
					"desc" => "eg. Mr Charlie H.D. Boddington. (This will be the title of the card)"
				),
				array(
					"name" => __("Qualifications", "cranleigh"),
					"id" => "{$prefix}qualifications",
					"type" => "text",
					"desc" => "eg. BA, DipEd, BEng, PhD"
				),
				array(
					"name" => __("Email Address", "cranleigh"),
					"id" => "{$prefix}email_address",
					"type" => "email",
					"desc" => "eg. djf@cranleigh.org"
				),
				array(
					"name" => __("Phone Number", "cranleigh"),
					"id" => "{$prefix}phone",
					"type" => "text",
					"desc" => "eg. 01483 542019"
				)
			),
			'validation' => array(
				'rules'    => array(
					"{$prefix}position" => [
						'required'  => true,
						'minlength' => 3,
					],
					"{$prefix}leadjobtitle" => [
						"required" => true,
						"minlength" => 3
					],
					"{$prefix}username" => [
						"required" => true
					],
					"{$prefix}surname" => [
						"required" => true
					]
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


		return $meta_boxes;
	}

	// Register Custom Post Type
	function CPT_Cranleigh_People() {

		$labels = array(
			'name'                  => _x( 'Cranleigh People', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Person', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'People', 'text_domain' ),
			'name_admin_bar'        => __( 'People', 'text_domain' ),
			'archives'              => __( 'All Staff', 'text_domain' ),
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
		$capabilities = array(
			'publish_posts' => 'publish_'.$this->post_type_key,
			'edit_posts' => 'edit_'.$this->post_type_key,
			'edit_others_posts' => 'edit_others_'.$this->post_type_key,
			'delete_posts' => 'delete_'.$this->post_type_key,
			'delete_others_posts' => 'delete_others_'.$this->post_type_key,
			'read_private_posts' => 'read_private_'.$this->post_type_key,
			'edit_post' => 'edit_'.$this->post_type_key,
			'delete_post' => 'delete_'.$this->post_type_key,
			'read_post' => 'read_'.$this->post_type_key,
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
			'menu_position'         => 27,
			'menu_icon'             => 'dashicons-businessman',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capabilities' => $capabilities,
		);
		register_post_type( $this->post_type_key, $args );
		$this->roles_and_caps();

	}
	function roles_and_caps() {
		$admin_role = get_role('administrator');
		$editor_role = get_role('editor');
		$caps = [
			"publish_".$this->post_type_key,
			"edit_".$this->post_type_key,
			"edit_others_".$this->post_type_key,
			"delete_".$this->post_type_key,
			"delete_others_".$this->post_type_key,
			"read_private_".$this->post_type_key,
			"edit_".$this->post_type_key,
			"read_".$this->post_type_key,
			"manage_staff_cats",
			"edit_staff_cats",
			"delete_staff_cats",
			"assign_staff_cats"
		];

		foreach ($caps as $cap):
			$editor_role->add_cap($cap);
			$admin_role->add_cap($cap);
		endforeach;

	}

	// Register Custom Taxonomy
	function TAX_staff_cats() {

		$labels = array(
			'name'                       => _x( 'Staff Roles', 'Taxonomy General Name', 'text_domain' ),
			'singular_name'              => _x( 'Staff Role', 'Taxonomy Singular Name', 'text_domain' ),
			'menu_name'                  => __( 'Staff Roles', 'text_domain' ),
			'all_items'                  => __( 'All Staff Roles', 'text_domain' ),
			'parent_item'                => __( 'Parent Group', 'text_domain' ),
			'parent_item_colon'          => __( 'Parent Group:', 'text_domain' ),
			'new_item_name'              => __( 'New Role', 'text_domain' ),
			'add_new_item'               => __( 'Add New Staff Role', 'text_domain' ),
			'edit_item'                  => __( 'Edit Staff Role', 'text_domain' ),
			'update_item'                => __( 'Update Staff Role', 'text_domain' ),
			'view_item'                  => __( 'View Staff Role', 'text_domain' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
			'add_or_remove_items'        => __( 'Add or remove staff roles', 'text_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
			'popular_items'              => __( 'Popular Roles', 'text_domain' ),
			'search_items'               => __( 'Search Roles', 'text_domain' ),
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
			'capabilities' => array(
				'manage_terms' => 'manage_staff_cats',
				'edit_terms' => 'edit_staff_cats',
				'delete_terms' => 'delete_staff_cats',
				'assign_terms' => 'assign_staff_cats',
			)
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
			unset( $columns['wpseo-score'] );
			unset( $columns['wpseo-title'] );
			unset( $columns['wpseo-metadesc'] );
			unset( $columns['wpseo-focuskw'] );

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
		wp_enqueue_script('cranleigh_people_media_button', plugins_url('javascripts/popme.js', CRAN_PEOPLE_FILE_PATH), array('jquery'), time(), true);
	}

	function add_mce_popup() {
		?>
		<script>
			function CranleighPeopleInsertShortcode(){

				var user = jQuery("#user").val();
				var title = jQuery("#card_title").val();
				var type = jQuery("#card_type").val();

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

	function admin_head() {
		global $pagenow;
		if (in_array($pagenow,['post.php','post-new.php']) && get_post_type()=='staff' && $this->isams_controlled===true) {

			echo '<style>
				.blink {
					animation-duration: 2s;
					animation-name: blink;
					animation-iteration-count: infinite;
					animation-timing-function: steps(4, start);
				}
				@keyframes blink {
				    80% {
				        visibility: hidden;
				    }
				}
				.wp-editor-tabs {
					display:none;
				}
				</style>';
			}

	}

	function force_default_editor() {
		return 'tinymce';
	}

	function admin_notice() {
		global $pagenow, $wpdb;
		if (in_array($pagenow, ['post.php', 'post-new.php']) && get_post_type()=='staff' && $this->isams_controlled===true) {
			// We have to use WPDB to get the staff username as the post data hasn't been called yet (we're in the admin!)
			$user = $wpdb->get_row("SELECT meta_value from $wpdb->postmeta WHERE post_id=".get_the_ID()." AND meta_key='staff_username'");
			echo '<div class="notice notice-warning"><p class="blink"><strong>Warning!</strong> This data is managed by a daily syncronisation from ISAMS. You can safely modify the profile photo. Any other changes you make will be overridden at the next sync.</p><p>To amend the biography <a target="_blank" href="https://marketing.cranleigh.org/staff-biographies/find/'.$user->meta_value.'">please click here.</a></div>';
		}
	}

}



