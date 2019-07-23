<?php

namespace CranleighSchool\CranleighPeople;

use WP_Query;
use WP_Error;

/*
 * TODO: Convert these shortcodes into a more SOLID principle design.
 */
class Shortcodes extends BaseController {

	public $default_attachment_id = false;

	function __construct() {
		$this->load();

		add_shortcode( 'person_card', [ $this, 'shortcode' ] );
		add_shortcode( 'card_list', [ $this, 'tutors_list' ] );
		add_shortcode( 'table_list', [ $this, 'table_list' ] );
		add_shortcode( 'people_taxonomy', [ $this, 'as_taxonomy' ] );
		add_shortcode( 'person_table', [ $this, 'table_list_shortcode' ] );
		add_shortcode( 'cran_person_table', [ $this, 'table_list_shortcode' ] );

		$this->query_args = [
			'post_type' => Plugin::POST_TYPE_KEY,
			'orderby'   => 'meta_value_num',
			'meta_key'  => Metaboxes::fieldID('username'),
		];

		if ( isset( $this->settings['default_photo_attachment_id'] ) ) :
			$this->default_attachment_id = $this->settings['default_photo_attachment_id'];
		else :
			$this->default_attachment_id = 32492;
		endif;

	}

	function table_list_row( $atts, $content = null ) {

		$atts = shortcode_atts(
			[
				'username' => null,
			],
			$atts
		);

		$posts = get_posts(
			[
				'post_type'      => Plugin::POST_TYPE_KEY,
				'posts_per_page' => - 1,
				'meta_key'       => Metaboxes::fieldID('username'),
				'meta_value'     => $atts['username'],
			]
		);
		if ( count( $posts ) == 1 ) {
			$post = $posts[0];
		} else {
			return new WP_Error(
				'Error getting User Data',
				'Could not locate data for the user: &quot;' . $atts['username'] . '&quot;.'
			);
		}
		$output = '';
		if ( get_post_status() == 'private' && current_user_can( 'manage_options' ) ) {
			$output .= '<tr class="danger">';
		} else {
			$output .= '<tr>';
		}

		$output .= '<td><a href="' . get_permalink( $post->ID ) . '"><span class="staff-title">' . get_post_meta(
			$post->ID,
			Metaboxes::fieldID('full_title'),
			true
		) . '</span></a><span class="qualifications">' . get_post_meta(
			$post->ID,
			Metaboxes::fieldID('qualifications'),
			true
		) . '</span></td>';
		$output .= '<td>' . get_post_meta( $post->ID, Metaboxes::fieldID('leadjobtitle'), true ) . '</td>';
		$output .= '</tr>';

		return $output;
	}

	function table_list_shortcode( $atts, $content = null ) {

		$atts      = shortcode_atts(
			[
				'users'        => null,
				'with_headers' => false,
			],
			$atts
		);
		$all_users = explode( ',', $atts['users'] );
		$users     = [];
		foreach ( $all_users as $user ) :
			$users[] = preg_replace( '/[^A-Za-z]/', '', trim( $user ) );
		endforeach;
		$args  = [
			'post_type'      => Plugin::POST_TYPE_KEY,
			'posts_per_page' => - 1,
			'orderby'        => 'meta_value',
			'meta_key'       => Metaboxes::fieldID('surname'),
			'order'          => 'ASC',
			'meta_query'     => [
				[
					'key'     => Metaboxes::fieldID('username'),
					'value'   => $users,
					'compare' => 'IN',
				],
			],
		];
		$staff = new WP_Query( wp_parse_args( $args, $this->query_args ) );
		ob_start();
		?>
		<div class="table-responsive">
			<table class="table table-condensed table-striped table-hover">
				<?php if ( $atts['with_headers'] !== false ) : ?>
					<thead>
					<th>Staff</th>
					<th>Job Title</th>
					</thead>
				<?php endif; ?>
				<tbody>
				<?php
				foreach ( $users as $user ) :
					$row = $this->table_list_row( [ 'username' => $user ] );
					if ( ! is_wp_error( $row ) ) {
						echo $row;
					} else {
						echo '<tr class="danger"><td colspan="2">' . $row->get_error_message() . '</td></tr>';
					}
					// echo $this->table_list_row(["username"=>$user]);
				endforeach;
				?>
				</tbody>
			</table>
		</div>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;

	}

	/**
	 * as_taxonomy function.
	 *
	 * @access public
	 *
	 * @param string|array $taxonomy
	 *
	 * @return void
	 */
	function as_taxonomy( $atts, $content = null ) {

		$atts  = shortcode_atts(
			[
				'taxonomy' => $taxonomy,
			],
			$atts
		);
		$args  = [
			'tax_query' => [
				[
					'taxonomy' => StaffCategoriesTaxonomy::TAXONOMY_KEY,
					'field'    => 'slug',
					'terms'    => $atts['taxonomy'],
				],
			],
		];
		$query = new WP_Query( wp_parse_args( $args, $this->query_args ) );
		while ( $query->have_posts() ) :
			$query->the_post();
			$people[] = get_post_meta( get_the_ID(), Metaboxes::fieldID('username'), true );
		endwhile;
		wp_reset_postdata();
		wp_reset_query();

		return $this->table_list( [ 'people' => implode( ',', $people ) ] );
	}

	function two_column( $post_id ) {

		global $post;

		$first_column = get_post_meta( $post->ID, Metaboxes::fieldID($this->first_column), true );
		$last_column  = get_post_meta( $post->ID, Metaboxes::fieldID($this->last_column), true );

		ob_start();

		?>
		<tr>
			<td><?php echo $first_column; ?></td>
			<td><?php echo $last_column; ?></td>
		</tr>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	function table_row( $atts, $content = null ) {

		$a = shortcode_atts(
			[
				'user'         => null,
				'first_column' => 'full_title',
				'last_column'  => 'email_address',
			],
			$atts
		);

		$this->first_column = $a['first_column'];
		$this->last_column  = $a['last_column'];

		return $this->shortcode(
			[
				'type' => 'two-column',
				'user' => $a['user'],
			]
		);
	}

	function table_list( $atts, $content = null ) {

		$a      = shortcode_atts(
			[
				'people'       => null,
				'class'        => 'table-striped',
				'first_column' => 'full_title',
				'last_column'  => 'email_address',
				'sort'         => false,
			],
			$atts
		);
		$people = explode( ',', $a['people'] );

		$users = [];
		foreach ( $people as $person ) :
			$initial          = str_split( $person );
			$last             = end( $initial );
			$users[ $person ] = $last;
		endforeach;

		if ( $a['sort'] == true ) {
			asort( $users );
		}

		ob_start();
		?>
		<table class="table <?php echo $a['class']; ?>">
			<?php
			foreach ( $users as $person => $dull ) {
				$username = trim( $person );
				echo $this->table_row( array_merge( [ 'user' => $username ], $a ) );
			}
			?>
		</table>
		<?php

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	function tutors_list( $atts ) {

		$a = shortcode_atts(
			[
				'people'  => null,
				'columns' => 2,
				'type'    => 'small',
				'sort'    => null,
			],
			$atts
		);

		switch ( $a['columns'] ) :
			case 2:
				$class = 6;
				break;
			case 3:
				$class = 4;
				break;
			default:
				$class = 6;
				break;
		endswitch;

		$people = explode( ',', $a['people'] );

		$users = [];
		foreach ( $people as $person ) :
			$initial          = str_split( $person );
			$last             = end( $initial );
			$users[ $person ] = $last;
		endforeach;

		if ( $a['sort'] == true ) {
			asort( $users );
		}

		ob_start();

		echo '<div class="row">';
		foreach ( $users as $person => $dull ) {
			$username = trim( $person );
			echo '<div class="col-sm-' . $class . '">';
			echo $this->shortcode(
				[
					'type' => $a['type'],
					'user' => $username,
				]
			);
			echo '</div>';
		}
		echo '</div>';

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	function small( $post_id, $card_title ) {

		global $post;
		$full_title = get_post_meta( $post->ID, Metaboxes::fieldID('full_title'), true );
		$phone      = get_post_meta( $post->ID, Metaboxes::fieldID('phone'), true );
		$phone_href = $this->phone_href( $phone );
		$position   = $this->get_position( get_post_meta( $post->ID, Metaboxes::fieldID('position'), true ) );
		if ( ! $position ) {
			$position = get_post_meta( $post->ID, Metaboxes::fieldID('leadjobtitle'), true );
		}
		$position = get_post_meta( $post->ID, Metaboxes::fieldID('leadjobtitle'), true );
		ob_start();
		?>
		<div class="card landscape">
			<div class="row">
				<div class="col-xs-4">
					<div class="card-image">
						<a href="<?php the_permalink(); ?>">
							<?php
							if ( has_post_thumbnail() ) :
								the_post_thumbnail( Plugin::PROFILE_PHOTO_SIZE_NAME, [ 'class' => 'img-responsive' ] );
							elseif ( $this->default_attachment_id !== null ) :
								$photo = wp_get_attachment_image(
									$this->default_attachment_id,
									Plugin::PROFILE_PHOTO_SIZE_NAME,
									false,
									[ 'class' => 'img-responsive' ]
								);
								echo $photo;
							endif;
							?>
						</a>
					</div>
				</div>
				<div class="col-xs-8">
					<div class="card-text">
						<h4><a href="<?php the_permalink(); ?>"><?php echo $full_title; ?></a></h4>
						<p><?php echo $position; ?></p>
					</div>
				</div>
			</div>
		</div>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	function shortcode( $atts, $content = null ) {

		$a = shortcode_atts(
			[
				'type'  => 'small',
				'user'  => null,
				'title' => null,
			],
			$atts
		);

		if ( $a['user'] === null ) {
			return '<div class="alert alert-warning">Staff member not specified.</div>';
		}
		if ( $a['type'] == 'house' && $a['title'] === null ) {
			return '<div class="alert alert-warning">Card title not specified.</div>';
		}

		$args = [
			'posts_per_page' => 1,
			'meta_query'     => [
				[
					'key'   => Metaboxes::fieldID('username'),
					'value' => $a['user'],
				],
			],
		];

		$this->switch_to_blog( $this->load_from_blog_id );
		$query = new WP_Query( wp_parse_args( $args, $this->query_args ) );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				$post_id = get_the_ID();

				switch ( $a['type'] ) :
					case 'biography-only':
						$output = $this->just_bio( $post_id );
						break;
					case 'house':
						$output = $this->house_staff( $post_id, $a['title'] );
						break;
					case 'hod':
						$output = $this->head_of_dept( $post_id, $a['title'] );
						break;
					case 'small':
						$output = $this->small( $post_id, $a['title'] );
						break;
					case 'two-column':
						$output = $this->two_column( $post_id );
						break;
					default:
						$output = $this->small( $post_id, $a['title'] );
						break;
				endswitch;
			endwhile;
			wp_reset_postdata();
			else :
				if ( ! wp_doing_ajax() ) :
					$output  = '<div class="alert alert-warning">Staff member &quot;' . $a['user'] . '&quot; not found.</div>';
					$slacker = new Slacker();
					$slacker->setUsername( 'Cranleigh People Error Catcher' );
					$slacker->post( 'The Cranleigh People Shortcode is trying to find `' . $a['user'] . '` but failing miserably! (' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ')' );
				endif;
		endif;

			$this->restore_current_blog();

			return $output;

	}

	function get_first_paragraph() {

		global $post;
		$str = wpautop( get_the_content() );
		$str = substr( $str, 0, strpos( $str, '</p>' ) + 4 );
		$str = strip_tags( $str, '<a><strong><em>' );

		if ( strlen( $this->get_second_paragraph() ) <= 1 && strlen( $str ) > 400 ) :
			return '<p class="biography">' . substr( $str, 0, 400 ) . '...</p>';
		else :
			return '<p class="biography">' . $str . '</p>';
		endif;
	}

	function get_second_paragraph() {

		global $post;
		$str = wpautop( get_the_content() );
		$str = substr( $str, strpos( $str, '</p>' ) + 4 );
		$str = strip_tags( $str, '<p><a><strong><em>' );

		return $str;
	}

	function default_card() {

		return 'Not Written Yet';
	}

	function phone_href( $number ) {

		return Helper::santitizePhoneHref( $number );
	}

	function get_position( $positions, $not = null ) {

		return Helper::santitizePositions( $positions, $not );
	}

	function card_title( $heading, $title ) {

		return '<' . $heading . '>' . $title . '</' . $heading . '>';
	}

	function sanitize_title_to_id( $card_title ) {

		$string = strtolower( str_replace( ' ', '', $card_title ) );

		return preg_replace( '/[^A-Za-z0-9\-]/', '', $string );
	}

	function get_staff_photo( $thumb = false ) {

		if ( has_post_thumbnail() ) :
			if ( $thumb === false ) :
				the_post_thumbnail( [ 600, 800 ], [ 'class' => 'img-responsive' ] );
			else :
				the_post_thumbnail( 'thumbnail', [ 'class' => 'img-responsive' ] );
			endif;
			else :
				$photo = wp_get_attachment_image(
					$this->default_attachment_id,
					Plugin::PROFILE_PHOTO_SIZE_NAME,
					false,
					[ 'class' => 'img-responsive' ]
				);
				echo $photo;
		endif;
	}

	function house_staff( $post_id = null, $card_title = 'Housemaster' ) {

		global $post;

		$full_title     = get_post_meta( $post->ID, Metaboxes::fieldID('full_title'), true );
		$phone          = get_post_meta( $post->ID, Metaboxes::fieldID('phone'), true );
		$phone_href     = $this->phone_href( $phone );
		$position       = $this->get_position( get_post_meta( $post->ID, Metaboxes::fieldID('position'), true ), 'Housemaster' );
		$email          = get_post_meta( $post->ID, Metaboxes::fieldID('email_address'), true );
		$qualifications = get_post_meta( $post->ID, Metaboxes::fieldID('qualifications'), true );
		ob_start();
		?>
		<section class="person-card" id="<?php echo $this->sanitize_title_to_id( $card_title ); ?>">
			<div class="card landscape light">
				<div class="row">
					<div class="col-xs-4">
						<div class="card-image">
							<?php $this->get_staff_photo(); ?>
						</div>
					</div>
					<div class="col-xs-8">
						<div class="card-text">
							<?php
							switch ( $card_title ) :
								case 'Housemaster':
								case 'Housemistress':
									echo $this->card_title( 'h2', $card_title );
									break;
								case 'Deputy Housemaster':
								case 'Deputy Housemistress':
								case 'Day Warden':
								case 'Matron':
									echo $this->card_title( 'h3', $card_title );
									break;
								default:
									echo $this->card_title( 'h3', $card_title );
							endswitch;
							?>
							<h4>
								<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $full_title; ?></a><span class="qualifications"><?php echo $qualifications; ?></span>
							</h4>
							<?php
							if ( $card_title !== 'Matron' ) :
								echo '<p><a href="mailto:' . $email . '"><span class="sr-only">E-mail:</span><span class="glyphicon glyphicon-envelope"></span>' . strtolower( $email ) . '</a>';
								if ( $phone ) :
									echo '<br>
								<a href="tel:' . $phone_href . '"><span class="sr-only">Phone:</span><span class="glyphicon glyphicon-earphone"></span>' . $phone . '</a></p>';
								endif;
							endif;
							echo $this->get_first_paragraph();

							if ( $this->get_second_paragraph() ) :
								?>
								<p class="read-more">
									<a href="#<?php echo $this->sanitize_title_to_id( $card_title ); ?>-bio" data-toggle="collapse" aria-controls="housemaster-bio" class="cranleigh-hide-readmore-link" aria-expanded="false">Read more…</a>
								</p>
								<div id="<?php echo $this->sanitize_title_to_id( $card_title ); ?>-bio" class="collapse" aria-expanded="false">
									<?php echo $this->get_second_paragraph(); ?>
								</div>
							<?php endif; ?>

						</div><!-- .card-text -->
					</div><!-- .xs-8 -->
				</div><!-- .row -->
			</div><!-- .card landscape light -->
		</section>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	function head_of_dept( $post_id = null, $card_title = 'Head of Department' ) {

		global $post;

		if ( empty( $card_title ) ) {
			$card_title = get_post_meta( $post->ID, Metaboxes::fieldID('leadjobtitle'), true );
		}

		$full_title     = get_post_meta( $post->ID, Metaboxes::fieldID('full_title'), true );
		$phone          = get_post_meta( $post->ID, Metaboxes::fieldID('phone'), true );
		$phone_href     = $this->phone_href( $phone );
		$position       = $this->get_position( get_post_meta( $post->ID, Metaboxes::fieldID('position'), true ), 'Housemaster' );
		$email          = get_post_meta( $post->ID, Metaboxes::fieldID('email_address'), true );
		$qualifications = get_post_meta( $post->ID, Metaboxes::fieldID('qualifications'), true );
		ob_start();
		?>
		<section id="<?php echo $this->sanitize_title_to_id( $card_title ); ?>">
			<div class="card landscape light">
				<div class="row">
					<div class="col-xs-4">
						<div class="card-image">
							<a href="<?php echo get_permalink( $post->ID ); ?>">
								<?php $this->get_staff_photo(); ?>
							</a>
						</div>
					</div>
					<div class="col-xs-8">
						<div class="card-text">
							<?php
							if ( $card_title !== null ) {
								echo $this->card_title( 'h3', $card_title );
							}
							?>
							<h4>
								<a class="email-link" href="mailto:<?php echo $email; ?>"><span class="sr-only">E-mail:</span><span class="glyphicon glyphicon-envelope"></span></a>
								<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $full_title; ?></a><span class="qualifications"><?php echo $qualifications; ?></span>
							</h4>
							<span class="hidden-xs">

								<?php echo $this->get_first_paragraph(); ?>

								<?php if ( strlen( $this->get_second_paragraph() ) > 1 ) : ?>
									<p class="read-more">
									<a href="#<?php echo $this->sanitize_title_to_id( $card_title ); ?>-<?php echo $post->ID; ?>-bio" data-toggle="collapse" aria-controls="housemaster-bio" class="cranleigh-hide-readmore-link" aria-expanded="false">Read more…</a>
								</p>
								<?php endif; ?>

								<div id="<?php echo $this->sanitize_title_to_id( $card_title ); ?>-<?php echo $post->ID; ?>-bio" class="collapse" aria-expanded="false">
									<?php echo $this->get_second_paragraph(); ?>
								</div>
								</span>
						</div><!-- .card-text -->
					</div><!-- .xs-8 -->
				</div><!-- .row -->
			</div><!-- .card landscape light -->
		</section>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	function just_bio( $post_id = null ) {

		global $post;
		$card_title = 'person-bio-' . $post_id;
		ob_start();
		?>
		<section class="biography-pullout" id="<?php echo $this->sanitize_title_to_id( $card_title ); ?>">
			<div class="pull-out">

				<?php echo $this->get_first_paragraph(); ?>

				<?php if ( strlen( $this->get_second_paragraph() ) > 1 ) : ?>
					<p class="read-more">
						<a href="#<?php echo $this->sanitize_title_to_id( $card_title ); ?>-bio" data-toggle="collapse" class="cranleigh-hide-readmore-link" aria-controls="person-bio" aria-expanded="false">Read more…</a>
					</p>
				<?php endif; ?>

				<div id="<?php echo $this->sanitize_title_to_id( $card_title ); ?>-bio" class="collapse" aria-expanded="false">
					<?php echo $this->get_second_paragraph(); ?>
				</div>

			</div>
		</section>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}


}
