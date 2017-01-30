<?php
	class Cranleigh_People_Shortcode {
		function __construct() {
			add_shortcode("person_card", array($this, 'shortcode'));
			add_shortcode("card_list", array($this, 'tutors_list'));
			add_shortcode("table_list", array($this, 'table_list'));

			$this->query_args = array(
				"post_type" => "staff",
				"orderby" => "meta_value_num",
				"meta_key" => "staff_username"
			);

		}
		function two_column($post_id) {
			global $post;

			$first_column = get_post_meta($post->ID, 'staff_'.$this->first_column, true);
			$last_column = get_post_meta($post->ID, 'staff_'.$this->last_column, true);

			ob_start();

?>			<tr>
				<td><?php echo $first_column; ?></td>
				<td><?php echo $last_column; ?></td>
			</tr>
<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		function table_row($atts, $content=null) {
			$a = shortcode_atts( [
				"user" => null,
				"first_column" => "full_title",
				"last_column" => "email_address"
			], $atts );

			$this->first_column = $a['first_column'];
			$this->last_column = $a['last_column'];

			return $this->shortcode(array("type" => "two-column", "user" => $a['user']));
		}

		function table_list($atts, $content=null) {
			$a = shortcode_atts(
				[
					"people" => null,
					"class" => "table-striped",
					"first_column" => "full_title",
					"last_column" => "email_address"
				],
				$atts);
			$people = explode(",", $a['people']);

			$users = array();
			foreach ($people as $person):
				$initial = str_split($person);
				$last = end($initial);
				$users[$person] = $last;
			endforeach;

			if ($a['sort']==true) {
				asort($users);
			}

			ob_start();
?>
			<table class="table <?php echo $a['class']; ?>">
<?php
			foreach ($users as $person => $dull) {
				$username = trim($person);
				echo "<div class=\"col-sm-".$class."\">";
				echo $this->table_row(array_merge(array("user" => $username), $a));
				echo "</div>";
			}
?>
			</table>
<?php

		$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		function tutors_list($atts) {
			$a = shortcode_atts(array(
				"people"=> null,
				"columns" => 2,
				"type" => "small",
				"sort" => null,
			), $atts);

			switch($a['columns']):
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

			$people = explode(",", $a['people']);

			$users = array();
			foreach ($people as $person):
				$initial = str_split($person);
				$last = end($initial);
				$users[$person] = $last;
			endforeach;

			if ($a['sort']==true) {
				asort($users);
			}

			ob_start();

			echo "<div class=\"row\">";
			foreach ($users as $person => $dull) {
				$username = trim($person);
				echo "<div class=\"col-sm-".$class."\">";
				echo $this->shortcode(array("type" => $a['type'], "user" => $username));
				echo "</div>";
			}
			echo "</div>";

			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		function small($post_id, $card_title) {
			global $post;
			$full_title = get_post_meta($post->ID, 'staff_full_title', true);
			$phone = get_post_meta($post->ID, 'staff_phone', true);
			$phone_href = $this->phone_href($phone);
			$position = $this->get_position(get_post_meta($post->ID, 'staff_position', true));
			if (!$position) {
				$position = get_post_meta($post->ID, 'staff_leadjobtitle', true);
			}
			ob_start();
			?>
			<div class="card landscape">
				<div class="row">
					<div class="col-xs-4">
						<div class="card-image">
							<?php if (has_post_thumbnail( )):
								the_post_thumbnail('thumbnail', array("class" => "img-responsive"));
								else:
									$photo = get_option("cran_people_basic")['default_photo'];
									echo "<img src=\"".$photo."\">";
								endif;
								?>
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

		function shortcode($atts, $content=null) {
			$a = shortcode_atts(array(
				'type' => 'small',
				'user' => null,
				'title' => null
			), $atts);

			if ($a['user']===null) {
				return "<div class=\"alert alert-warning\">Staff member not specified.</div>";
			}
			if ($a['type']=="house" && $a['title']===null) {
				return "<div class=\"alert alert-warning\">Card title not specified.</div>";
			}

			$args = array(
				"posts_per_page" => 1,
				"meta_query" => array(
					array(
						"key" => "staff_username",
						"value" => $a['user']
					)
				)
			);

			switch_to_blog(BLOG_ID_CURRENT_SITE);
			$query = new WP_Query(wp_parse_args($args, $this->query_args));

			if ($query->have_posts()):
				while($query->have_posts()): $query->the_post();
					$post_id = get_the_ID();

					switch($a['type']):
						case "house":
							$output = $this->house_staff($post_id, $a['title']);
						break;
						case "hod":
							$output = $this->head_of_dept($post_id, $a['title']);
						break;
						case "small":
							$output = $this->small($post_id, $a['title']);
						break;
						case "two-column":
							$output = $this->two_column($post_id);
						break;
						default:
							$output = $this->small($post_id, $a['title']);
						break;
					endswitch;
				endwhile;
				wp_reset_postdata();
			else:
				$output = "<div class=\"alert alert-warning\">Staff member &quot;".$a['user']."&quot; not found.</div>";
			endif;

			restore_current_blog();

			return $output;

		}

		function get_first_paragraph() {
			global $post;
			$str = wpautop(get_the_content());
			$str = substr($str, 0, strpos($str, '</p>') + 4);
			$str = strip_tags($str, '<a><strong><em>');

			if (strlen($this->get_second_paragraph()) <= 1 && strlen($str) > 400):
				return '<p class="biography">'.substr($str, 0, 400).'...</p>';
			else:
				return '<p class="biography">' . $str . '</p>';
			endif;
		}
		function get_second_paragraph() {
			global $post;
			$str = wpautop(get_the_content());
			$str = substr($str, strpos($str, '</p>')+4);
			$str = strip_tags($str, '<p><a><strong><em>');

			return $str;
		}

		function default_card() {
			return 'Not Written Yet';
		}

		function phone_href($number) {
			if (substr($number, 0, 1)=="+") {
				return $number;
			} else {
				$str = str_replace("01483", "+441483", $number);
				$str = str_replace(" ", "", $str);
				return $str;
			}
		}
		function get_position($positions, $not=null) {
			if (is_array($positions)):
			foreach ($positions as $position):
				if ($position == $not):
					continue;
				endif;
				break;;
			endforeach;
			return $position;
			endif;
			return false;
		}

		function card_title($heading, $title) {
			return "<".$heading.">".$title."</".$heading.">";
		}
		function sanitize_title_to_id($card_title) {
			return strtolower(str_replace(" ", "", $card_title));
		}
		function get_staff_photo($thumb=false) {
			if (has_post_thumbnail()):
				if ($thumb===false) {
					the_post_thumbnail(array(600,800), array("class"=>"img-responsive"));
				} else {
					the_post_thumbnail('thumbnail', array("class"=>"img-responsive"));
				}
			else:
				$photo = get_option("cran_people_basic")['default_photo'];
				echo "<img src=\"".$photo."\">";
			endif;
		}

		function house_staff($post_id=null, $card_title="Housemaster") {
			global $post;

			$full_title = get_post_meta($post->ID, 'staff_full_title', true);
			$phone = get_post_meta($post->ID, 'staff_phone', true);
			$phone_href = $this->phone_href($phone);
			$position = $this->get_position(get_post_meta($post->ID, 'staff_position', true), "Housemaster");
			$email = get_post_meta($post->ID, 'staff_email_address', true);
			ob_start();
			?>
				<section id="<?php echo $this->sanitize_title_to_id($card_title); ?>">
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
										switch($card_title):
											case "Housemaster":
											case "Housemistress":
												echo $this->card_title('h2', $card_title);
											break;
											case "Deputy Housemaster":
											case "Deputy Housemistress":
											case "Day Warden":
											case "Matron":
												echo $this->card_title('h3', $card_title);
											break;
											default:
												echo $this->card_title('h3', $card_title);
										endswitch;
									?>
									<h4><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $full_title; ?></a></h4>
									<?php
										if ($card_title !== "Matron"):
											echo '<p><a href="mailto:'.$email.'"><span class="sr-only">E-mail:</span><span class="glyphicon glyphicon-envelope"></span>'.strtolower($email).'</a>';
											if ($phone):
												echo '<br>
									<a href="tel:'.$phone_href.'"><span class="sr-only">Phone:</span><span class="glyphicon glyphicon-earphone"></span>'.$phone.'</a></p>';
											endif;
										endif;
									echo $this->get_first_paragraph(); ?>

									<p class="read-more"><a href="#<?php echo $this->sanitize_title_to_id($card_title);?>-bio" data-toggle="collapse" aria-controls="housemaster-bio" aria-expanded="false">Read more…</a></p>
									<div id="<?php echo $this->sanitize_title_to_id($card_title); ?>-bio" class="collapse" aria-expanded="false">
										<?php echo $this->get_second_paragraph(); ?>
									</div>

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

		function head_of_dept($post_id=null, $card_title="Head of Department") {
			global $post;

			$full_title = get_post_meta($post->ID, 'staff_full_title', true);
			$phone = get_post_meta($post->ID, 'staff_phone', true);
			$phone_href = $this->phone_href($phone);
			$position = $this->get_position(get_post_meta($post->ID, 'staff_position', true), "Housemaster");
			ob_start();
			?>
				<section id="<?php echo $this->sanitize_title_to_id($card_title); ?>">
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
									if ($card_title !== null) {
										echo $this->card_title('h3', $card_title);
									}
								?>
									<h4><a href="mailto:<?php echo get_post_meta($post->ID, 'staff_email_address', true); ?>"><span class="sr-only">E-mail:</span><span class="glyphicon glyphicon-envelope"></span></a> <a href="<?php echo get_permalink($post->ID); ?>"><?php echo $full_title; ?></a></h4>

									<?php echo $this->get_first_paragraph(); ?>

									<?php if (strlen($this->get_second_paragraph()) > 1): ?>
									<p class="read-more">
										<a href="#<?php echo $this->sanitize_title_to_id($card_title);?>-bio" data-toggle="collapse" aria-controls="housemaster-bio" aria-expanded="false">Read more…</a>
									</p>
									<?php endif; ?>

									<div id="<?php echo $this->sanitize_title_to_id($card_title); ?>-bio" class="collapse" aria-expanded="false">
										<?php echo $this->get_second_paragraph(); ?>
									</div>
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
new Cranleigh_People_Shortcode();
