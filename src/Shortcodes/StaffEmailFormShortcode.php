<?php


	namespace CranleighSchool\CranleighPeople\Shortcodes;


	use CranleighSchool\CranleighPeople\Metaboxes;
	use RGFormsModel;

	class StaffEmailFormShortcode extends BaseShortcode
	{
		public function tagName(): string
		{
			return 'staff-email-form';
		}

		public function handle(array $atts, $content = NULL)
		{
			$atts = shortcode_atts(
				[
					'staff_id' => NULL,
				],
				$atts
			);
			if ($atts['staff_id'] === NULL) {
				echo '<div class="alert alert-danger">Could not load form, as Staff ID was not set</div>';
			} else {
				$staff_email = get_post_meta($atts['staff_id'], Metaboxes::fieldID('email_address'), true);
				$form_id = RGFormsModel::get_form_id('Staff Email Form');
				echo do_shortcode("[gravityforms title='false' description='false' field_values='staff_email=" . $staff_email . "' id=" . $form_id . "]");
			}
		}
	}
