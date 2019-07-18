<?php

namespace CranleighSchool\CranleighPeople;

use WP_Widget;
use WP_Query;

class Cranleigh_People_Widget extends WP_Widget {

	public $cranleigh_people_settings;
	public $load_from_blog_id;
	private $query_args = [];

	public function __construct() {

		$this->load_from_blog_id         = Plugin::getPluginSetting('load_from_blog_id');

		$widget_ops = [
			'classname'   => 'person-card',
			'description' => 'Shows a widget for a person.',
		];

		parent::__construct( 'cranleigh-person', 'Cranleigh Person Card', $widget_ops );

		$this->query_args = [
			'posts_per_page' => - 1,
			'post_type'      => Plugin::POST_TYPE_KEY,
			'orderby'        => 'meta_value_num',
			'meta_key'       => Metaboxes::fieldID('username'),
		];

	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters(
				'widget_title',
				$instance['title']
			) . $args['after_title'];
		}
		if ( isset( $instance['condensed'] ) && $instance['condensed'] == true ) :
			echo do_shortcode( '[person_card type=small user=' . $instance['username'] . ']' );
		else :
			$this->html( $instance['username'] );
		endif;

		echo $args['after_widget'];
	}

	/**
	 * @param string $username
	 */
	public function html( string $username ) {

		$args = [
			'posts_per_page' => 1,
			'meta_query'     => [
				[
					'key'   => Metaboxes::fieldID('username'),
					'value' => $username,
				],
			],
		];

		BaseController::switch_to_blog( $this->load_from_blog_id );
		$query = new WP_Query( wp_parse_args( $args, $this->query_args ) );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();

				?><?php edit_post_link( '[Edit ' . $username . ']', "<small class='pull-right'>", '</small>' ); ?>
				<h5>
					<a href="<?php the_permalink(); ?>">
						<span class="glyphicon glyphicon-envelope"></span>

						<?php echo get_post_meta( get_the_ID(), Metaboxes::fieldID('full_title'), true ); ?></a>

				</h5>
				<div class="person-image">
					<?php
					if ( has_post_thumbnail() ) :
						the_post_thumbnail(
							Plugin::PROFILE_PHOTO_SIZE_NAME,
							[ 'class' => 'img-responsive' ]
						); // This needs to not be `full` but we haven't confirmed image sizes yet
					endif;
					?>
				</div>
				<?php

				echo $this->get_first_paragraph();

			endwhile;

			else :
				echo '<p class="alert-warning">Staff Member not found</p>';
				$slacker = new Slacker();
				$slacker->setUsername( 'Cranleigh People Error Catcher' );
				$slacker->post( '<!everyone> The Cranleigh People Widget is trying to find `' . $username . '` but failing miserably! (' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ')' );
		endif;
			wp_reset_postdata();
			BaseController::restore_current_blog();
			echo '<div class="clearfix clear">&nbsp;</div>';
	}

	/**
	 * @return string
	 */
	public function get_first_paragraph() {

		global $post;
		$str = wpautop( get_the_content() );
		$str = substr( $str, 0, strpos( $str, '</p>' ) + 4 );
		$str = strip_tags( $str, '<a><strong><em>' );

		return '<p class="biography">' . $str . '</p>';
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array instance
	 */
	public function update( $new_instance, $old_instance ) {

		$instance             = [];
		$instance['title']    = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['username'] = ( ! empty( $new_instance['username'] ) ) ? strip_tags( $new_instance['username'] ) : '';

		return $instance;
	}

	/**
	 * @param array $instance
	 */
	public function form( $instance ) {

		$title    = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Staff Member', 'cranleigh-2016' );
		$username = ! empty( $instance['username'] ) ? $instance['username'] : '';

		if ( $this->load_from_blog_id == 1 ) :
			echo '<p class="notice-error notice">Warning: You need to set which Blog you want to pull your data from. Your widget may not display correctly on the frontend until you do this. Visit the <a href="options-general.php?page=cranleigh_people_settings">Cranleigh People Settings Page</a>.</p>';
		endif;
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php _e( esc_attr( 'Username:' ) ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>">
				<option value="">Select User</option>
				<?php

				BaseController::switch_to_blog( $this->load_from_blog_id );
				$query = new WP_Query( $this->query_args );

				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();
						$staff_username = get_post_meta( get_the_ID(), Metaboxes::fieldID('username'), true );
						if ( $staff_username === $username ) {
							$selected = 'selected="selected"';
						} else {
							$selected = null;
						}
						echo '<option value="' . $staff_username . '" ' . $selected . '>' . strtoupper( $staff_username ) . ' (' . get_the_title() . ')' . '</option>';
					endwhile;
					wp_reset_postdata();
					else :
						echo 'You have no staff to choose from...';
				endif;
					BaseController::restore_current_blog();

					?>
			</select>
		</p>
		<?php
	}

}

