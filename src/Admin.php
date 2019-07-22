<?php


	namespace CranleighSchool\CranleighPeople;


	use CranleighSchool\CranleighPeople\Importer\Importer;
	use WP_Query;

	class Admin
	{
		public static $card_types;

		public static function register()
		{
			self::$card_types = (object)[
				[
					'value' => 'hod',
					'title' => 'Head of Department',
				],
				[
					'value' => 'house',
					'title' => 'House',
				],
				[
					'value' => 'small',
					'title' => 'Small',
				],
				[
					'value' => 'two-column',
					'title' => 'Two Column',
				],
			];

			$instance = new self();

			add_filter('enter_title_here', [$instance, 'title_text_input']);

			add_filter('manage_edit-staff_columns', [$instance, 'add_photo_column_to_listing']);
			add_action('manage_posts_custom_column', [$instance, 'add_photo_to_listing'], 10, 2);

			add_action('media_buttons', [$instance, 'add_media_button'], 900);
			add_action('wp_enqueue_media', [$instance, 'include_media_button_js_file']);
			add_action('admin_print_footer_scripts', [$instance, 'add_mce_popup']);


			add_action('admin_notices', [$instance, 'admin_notice']);
			add_action('admin_head', [$instance, 'admin_head']);

			add_action("after_setup_theme", [$instance, "manual_importer"]);


			$instance->tinymceFilters();

			if (Plugin::getPluginSetting('load_cpt', true) === false) {
				add_action('admin_notices', [$instance, 'notice_no_settings']);
			}

		}

		public function tinymceFilters()
		{

			// Force TinyMce on people
			add_filter('wp_default_editor', function () {
				if (get_post_type() == CustomPostType::POST_TYPE_KEY) {
					return 'tinymce';
				}
			});

			add_filter(
				'tiny_mce_before_init',
				function ($args) {
					global $pagenow;
					if (get_post_type() == CustomPostType::POST_TYPE_KEY) {

						if ($pagenow == 'post.php') {
							$args['readonly'] = true;
							$args['toolbar'] = false;
						}
					}

					return $args;
				}
			);


		}

		public function manual_importer()
		{
			if (method_exists(\CranleighSchool\CranleighPeople\Importer\Admin::class, 'add_submenu_page')) {
				\CranleighSchool\CranleighPeople\Importer\Admin::add_submenu_page();
			}
		}

		public function notice_no_settings()
		{

			echo '<div class="notice notice-warning"><p><strong>Cranleigh People:</strong> You need to save your Cranleigh People Settings. Please <a href="' . menu_page_url(
					'cranleigh_people_settings',
					false
				) . '">click here</a></p></div>';
		}

		function add_photo_column_to_listing($defaults)
		{

			if (get_post_type() == CustomPostType::POST_TYPE_KEY) {
				$columns = [];
				$columns['cb'] = $defaults['cb'];
				$columns['title'] = $defaults['title'];
				$columns['staff_username'] = 'Username';
				$columns['staff_leadjobtitle'] = 'Lead Job Title';
				$columns[ 'taxonomy-' . StaffCategoriesTaxonomy::TAXONOMY_KEY ] = $defaults[ 'taxonomy-' . StaffCategoriesTaxonomy::TAXONOMY_KEY ];
				$columns['date'] = $defaults['date'];
				$columns['staff_photo'] = 'Photo';
				unset($columns['wpseo-score']);
				unset($columns['wpseo-title']);
				unset($columns['wpseo-metadesc']);
				unset($columns['wpseo-focuskw']);

				return $columns;
			}

			return $defaults;
		}

		function add_photo_to_listing($column_name, $post_ID)
		{

			if ($column_name == 'staff_photo') {
				$post_featured_image = $this->get_staff_photo($post_ID);
				if ($post_featured_image) {
					echo $post_featured_image;
				}
			}
			if ($column_name == 'staff_username') {
				echo strtoupper(get_post_meta($post_ID, 'staff_username', true));
			}
			if ($column_name == 'staff_leadjobtitle') {
				echo get_post_meta($post_ID, 'staff_leadjobtitle', true);
			}
		}

		function get_staff_photo($post_ID)
		{

			$post_thumb_id = get_post_thumbnail_id($post_ID);
			if ($post_thumb_id) {
				return get_the_post_thumbnail($post_ID, [100, 100]);
				$post_thumb_img = wp_get_attachment_image_src($post_thumb_id, [100, 100]);

				return $post_thumb_img[0];
			}
		}

		function add_media_button()
		{

			echo '<style>.wp-media-buttons .person_card_insert span.wp-media-buttons-icon:before {
			font:400 18px/1 dashicons;
			content:"\f110";
			} </style>';
			echo '<a href="#" class="button person_card_insert" id="add_person_shortcode"><span class="wp-media-buttons-icon"></span>' . esc_html__(
					'Person Card',
					'cranleigh'
				) . '</a>';

		}

		function include_media_button_js_file()
		{

			wp_enqueue_script(
				'cranleigh_people_media_button',
				plugins_url('javascripts/popme.js', CRAN_PEOPLE_FILE_PATH),
				['jquery'],
				time(),
				true
			);
		}

		function add_mce_popup()
		{

			?>
			<script>
				function CranleighPeopleInsertShortcode() {

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
						<label>User</label><br/>
						<?php
							$args = [
								'post_type'      => CustomPostType::POST_TYPE_KEY,
								'posts_per_page' => -1,
								'meta_key'       => 'staff_surname',
								'orderby'        => 'meta_value',
								'order'          => 'ASC',
							];

							$newquery = new WP_Query($args);

						?>
						<select id="user">
							<option value="">--SELECT A STAFF MEMBER---</option>
							<?php
								while ($newquery->have_posts()) :
									$newquery->the_post();
									$username = get_post_meta(get_the_ID(), 'staff_username', true);
									?>
									<option
										value="<?php echo $username; ?>"><?php echo get_the_title() . ' (' . $username . ')'; ?></option>
								<?php
								endwhile;
								wp_reset_postdata();
								wp_reset_query();
							?>
						</select>
						<br/>
						<label>Card Type</label><br/>
						<select id="card_type">
							<option value="">--SELECT A CARD TYPE---</option>
							<?php
								foreach (self::$card_types as $card_type) :
									?>
									<option
										value="<?php echo $card_type['value']; ?>"><?php echo $card_type['title']; ?></option>
								<?php endforeach; ?>
						</select>
						<br/>
						<label>Card Title</label><br/>
						<input type="text" id="card_title" style="padding:5px;width:100%;border-radius: 5px;"
							   placeholder="Card Title"/>

						<div style="padding-top:15px;">
							<input type="button" class="button-primary" value="Insert Shortcode"
								   onclick="CranleighPeopleInsertShortcode();"/>
							<a class="button" href="#" onclick="tb_remove(); return false;">
								<?php _e('Cancel', 'js_shortcode'); ?>
							</a>
						</div>

					</div>
				</div>
			</div>

			<?php
		}

		function admin_head()
		{

			global $pagenow;
			if (in_array(
					$pagenow,
					['post.php', 'post-new.php']
				) && get_post_type() == CustomPostType::POST_TYPE_KEY
			) {

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

		function admin_notice()
		{

			global $pagenow, $wpdb;
			if (in_array(
					$pagenow,
					['post.php', 'post-new.php']
				) && get_post_type() == CustomPostType::POST_TYPE_KEY
			) {

				echo '<div class="notice notice-warning"><p class="blink"><strong>STOP!</strong> You are strongly advised not to edit data here. The correct place is on the <a href="https://people.cranleigh.org/" target="_blank">Cranleigh People Manager</a>.</p></div>';
			}
		}

		/**
		 * title_text_input function.
		 *
		 * @access public
		 *
		 * @param mixed $title
		 *
		 * @return void
		 */
		function title_text_input($title)
		{

			if (get_post_type() == CustomPostType::POST_TYPE_KEY) :
				return $title = '(first name) (surname)';
			endif;

			return $title;
		}
	}
