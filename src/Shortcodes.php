<?php

namespace CranleighSchool\CranleighPeople;

	use CranleighSchool\CranleighPeople\Shortcodes\DynamicTableListShortcode;
	use CranleighSchool\CranleighPeople\Shortcodes\PersonTableShortcode;
	use CranleighSchool\CranleighPeople\Shortcodes\StaffEmailFormShortcode;
	use CranleighSchool\CranleighPeople\Shortcodes\TaxonomyShortcode;
	use CranleighSchool\CranleighPeople\Shortcodes\TutorListShortcode;
	use CranleighSchool\CranleighPeople\Traits\ShortcodeTrait;
	use WP_Query;

	/*
	 * TODO: Convert these shortcodes into a more SOLID principle design.
	 */

	/**
	 * Class Shortcodes.
	 */
class Shortcodes extends BaseController {

	use ShortcodeTrait;

	/**
	 * @var bool|int
	 */
	public $default_attachment_id = false;

	public $query_args = array();

	/**
	 * Shortcodes constructor.
	 */
	public function __construct() {
		$this->load();

		add_shortcode( 'person_card', array( $this, 'shortcode' ) );
		TaxonomyShortcode::register();
		PersonTableShortcode::register();
		TutorListShortcode::register();
		DynamicTableListShortcode::register();
		StaffEmailFormShortcode::register();

		$this->query_args = array(
			'post_type' => Plugin::POST_TYPE_KEY,
			'orderby'   => 'meta_value_num',
			'meta_key'  => Metaboxes::fieldID( 'username' ),
		);

		if ( isset( $this->settings['default_photo_attachment_id'] ) ) {
			$this->default_attachment_id = $this->settings['default_photo_attachment_id'];
		} else {
			$this->default_attachment_id = 32492;
		}
	}

	/**
	 * @param array $atts
	 * @param null  $content
	 *
	 * @return string
	 */
	public function shortcode( array $atts, $content = null ): string {
		$a = shortcode_atts(
			array(
				'type'  => 'small',
				'user'  => null,
				'title' => null,
			),
			$atts
		);

		if ( $a['user'] === null ) {
			return '<div class="alert alert-warning">Staff member not specified.</div>';
		}
		if ( $a['type'] == 'house' && $a['title'] === null ) {
			return '<div class="alert alert-warning">Card title not specified.</div>';
		}

		$args = array(
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'   => Metaboxes::fieldID( 'username' ),
					'value' => $a['user'],
				),
			),
		);

		self::switch_to_blog( $this->load_from_blog_id );
		$query = new WP_Query( wp_parse_args( $args, $this->query_args ) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();

				switch ( $a['type'] ) {
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
				}
			}
			wp_reset_postdata();
		} else {
			if ( ! wp_doing_ajax() ) {
				$output = '<div class="alert alert-warning">Staff member &quot;' . $a['user'] . '&quot; not found.</div>';
				$slacker = new Slacker();
				$slacker->setUsername( 'Cranleigh People Error Catcher' );
				$slacker->post( 'The Cranleigh People Shortcode is trying to find `' . $a['user'] . '` but failing miserably! (' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ')' );
			}
		}

		self::restore_current_blog();

		return $output;
	}

	/**
	 * @param null $post_id
	 *
	 * @return false|string
	 */
	public function just_bio( int $post_id ): string {
		$card_title = 'person-bio-' . $post_id;

		return View::render( 'biography-only', compact( 'card_title', 'post_id' ) );
	}

	/**
	 * @param null   $post_id
	 * @param string $card_title
	 *
	 * @return string
	 */
	public function house_staff( $post_id = null, string $card_title = 'Housemaster' ): string {
		global $post;

		$full_title = get_post_meta( $post_id, Metaboxes::fieldID( 'full_title' ), true );
		$phone = get_post_meta( $post_id, Metaboxes::fieldID( 'phone' ), true );
		$phone_href = $this->phone_href( $phone );
		$position = $this->get_position( get_post_meta( $post_id, Metaboxes::fieldID( 'position' ), true ), 'Housemaster' );
		$email = get_post_meta( $post_id, Metaboxes::fieldID( 'email_address' ), true );
		$qualifications = get_post_meta( $post_id, Metaboxes::fieldID( 'qualifications' ), true );
		ob_start(); ?>
			<section class="person-card" id="<?php echo self::sanitize_title_to_id( $card_title ); ?>">
				<div class="card landscape light">
					<div class="row">
						<div class="col-xs-4">
							<div class="card-image">
							<?php View::the_post_thumbnail(); ?>
							</div>
						</div>
						<div class="col-xs-8">
							<div class="card-text">
							<?php
							switch ( $card_title ) {
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
							}
							?>
								<h4>
									<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $full_title; ?></a><span
										class="qualifications"><?php echo $qualifications; ?></span>
								</h4>
								<?php
								if ( $card_title !== 'Matron' ) {
									echo '<p><a href="mailto:' . $email . '"><span class="sr-only">E-mail:</span><span class="glyphicon glyphicon-envelope"></span>' . strtolower( $email ) . '</a>';
									if ( $phone ) {
										echo '<br>
								<a href="tel:' . $phone_href . '"><span class="sr-only">Phone:</span><span class="glyphicon glyphicon-earphone"></span>' . $phone . '</a></p>';
									}
								}
								echo self::get_first_paragraph();

								if ( self::get_second_paragraph() ) {
									?>
										<p class="read-more">
											<a href="#<?php echo self::sanitize_title_to_id( $card_title ); ?>-bio"
											   data-toggle="collapse" aria-controls="housemaster-bio"
											   class="cranleigh-hide-readmore-link" aria-expanded="false">Read more…</a>
										</p>
										<div id="<?php echo self::sanitize_title_to_id( $card_title ); ?>-bio"
											 class="collapse" aria-expanded="false">
											<?php echo self::get_second_paragraph(); ?>
										</div>
									<?php
								}
								?>

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

	/**
	 * @param null   $post_id
	 * @param string $card_title
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function head_of_dept( $post_id = null, string $card_title = 'Head of Department' ): string {
		global $post;

		if ( empty( $card_title ) ) {
			$card_title = get_post_meta( $post->ID, Metaboxes::fieldID( 'leadjobtitle' ), true );
		}

		$full_title = get_post_meta( $post->ID, Metaboxes::fieldID( 'full_title' ), true );
		$phone = get_post_meta( $post->ID, Metaboxes::fieldID( 'phone' ), true );
		$phone_href = $this->phone_href( $phone );
		$position = $this->get_position( get_post_meta( $post->ID, Metaboxes::fieldID( 'position' ), true ), 'Housemaster' );
		$email = get_post_meta( $post->ID, Metaboxes::fieldID( 'email_address' ), true );
		$qualifications = get_post_meta( $post->ID, Metaboxes::fieldID( 'qualifications' ), true );
		ob_start();
		?>
			<section id="<?php echo self::sanitize_title_to_id( $card_title ); ?>">
				<div class="card landscape light">
					<div class="row">
						<div class="col-xs-4">
							<div class="card-image">
								<a href="<?php echo get_permalink( $post->ID ); ?>">
								<?php View::the_post_thumbnail(); ?>
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
									<a class="email-link" href="mailto:<?php echo $email; ?>"><span class="sr-only">E-mail:</span><span
											class="glyphicon glyphicon-envelope"></span></a>
									<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $full_title; ?></a><span
										class="qualifications"><?php echo $qualifications; ?></span>
								</h4>
								<span class="hidden-xs">

								<?php echo self::get_first_paragraph(); ?>

									<?php if ( strlen( self::get_second_paragraph() ) > 1 ) { ?>
										<p class="read-more">
									<a href="#<?php echo self::sanitize_title_to_id( $card_title ); ?>-<?php echo $post->ID; ?>-bio"
									   data-toggle="collapse" aria-controls="housemaster-bio"
									   class="cranleigh-hide-readmore-link" aria-expanded="false">Read more…</a>
								</p>
									<?php } ?>

								<div
									id="<?php echo self::sanitize_title_to_id( $card_title ); ?>-<?php echo $post->ID; ?>-bio"
									class="collapse" aria-expanded="false">
									<?php echo self::get_second_paragraph(); ?>
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
}
