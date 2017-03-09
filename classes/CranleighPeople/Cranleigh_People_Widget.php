<?php
namespace CranleighPeople;

use WP_Widget;
use WP_Query;

class Cranleigh_People_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname' => 'person-card',
			'description' => 'Shows a widget for a person.'
		);

		parent::__construct('cranleigh-person', 'Cranleigh Person Card', $widget_ops);

		$this->query_args = array(
			"posts_per_page" => -1,
			"post_type" => "staff",
			"orderby" => "meta_value_num",
			"meta_key" => "staff_username"
		);

	}

	function widget($args, $instance) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$this->html($instance['username']);

		echo $args['after_widget'];
	}

	function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['username'] = ( ! empty( $new_instance['username'] )) ? strip_tags( $new_instance['username'] ) : '';

		return $instance;
	}

	function form($instance) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Staff Member', 'cranleigh-2016' );
		$username = ! empty($instance['username']) ? $instance['username'] : "";
		?>
		<p>Custom Email, Phone and address first line can be defined in the <a href="customize.php">Theme Customiser</a></p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('username')); ?>"><?php _e(esc_attr('Username:')); ?></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('username')); ?>" name="<?php echo esc_attr($this->get_field_name('username')); ?>">
				<option value="">Select User</option>
				<?php

					BaseController::switch_to_blog(BLOG_ID_CURRENT_SITE);
					$query = new WP_Query($this->query_args);

					if ($query->have_posts()):
						while($query->have_posts()): $query->the_post();
							$staff_username = get_post_meta(get_the_ID(), 'staff_username', true);
							if ($staff_username === $username) {
								$selected = "selected=\"selected\"";
							} else {
								$selected = null;
							}
							echo "<option value=\"".$staff_username."\" ".$selected.">".strtoupper($staff_username)." (".get_the_title().")"."</option>";
						endwhile;
						wp_reset_postdata();
					else:
						echo 'You have no staff to choose from...';
					endif;
					BaseController::restore_current_blog();

				?>
			</select>
		</p>
		<?php
	}
	function html($username) {
		$args = array(
			"posts_per_page" => 1,
			"meta_query" => array(
				array(
					"key" => "staff_username",
					"value" => $username
				)
			)
		);
		BaseController::switch_to_blog(BLOG_ID_CURRENT_SITE);
		$query = new WP_Query(wp_parse_args($args, $this->query_args));

		if ($query->have_posts()):
			while($query->have_posts()): $query->the_post();

		?><?php edit_post_link("[Edit ".$username."]", "<small class='pull-right'>", "</small>"); ?>
			<h5>
				<a href="<?php the_permalink(); ?>">
					<span class="glyphicon glyphicon-envelope"></span>

				<?php echo get_post_meta(get_the_ID(), 'staff_full_title', true); ?></a>

			</h5>
			<div class="person-image">
				<?php
					if (has_post_thumbnail()):
						the_post_thumbnail('full', array("class"=>"img-responsive")); // This needs to not be `full` but we haven't confirmed image sizes yet
					else:
						$check = wp_remote_head(site_url("staff_photos/database.php?user_=".$username), ['timeout'=>5]);
						if ($check['response']['code']==200):
							echo "<img class=\"img-responsive\" alt=\"".get_the_title()."\" src=\"".site_url("staff_photos/database.php?user_=".$username)."\" />";
						endif;
					endif;
					?>
			</div>
			<?php

			echo $this->get_first_paragraph();

			endwhile;
			wp_reset_postdata();
			BaseController::restore_current_blog();
		else:
			echo "<p class=\"alert-warning\">Staff Member not selected</p>";
		endif;
		echo "<div class=\"clearfix clear\">&nbsp;</div>";
	}

		function get_first_paragraph() {
			global $post;
			$str = wpautop(get_the_content());
			$str = substr($str, 0, strpos($str, '</p>') + 4);
			$str = strip_tags($str, '<a><strong><em>');

			return '<p class="biography">' . $str . '</p>';
		}

}

