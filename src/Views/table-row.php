<?php
	use CranleighSchool\CranleighPeople\Metaboxes;
	use CranleighSchool\CranleighPeople\Shortcodes\DynamicTableListShortcode;
?>
<tr>
	<td><?php echo get_post_meta($staff_member->ID, Metaboxes::fieldID($first_column), true); ?></td>
	<td>
		<?php

			if ($last_column == 'full_title') {
				echo DynamicTableListShortcode::get_formatted_full_title($staff_member);
			} else {
				?>
				<?php echo get_post_meta($staff_member->ID, Metaboxes::fieldID($last_column), true); ?>
			<?php } ?>
	</td>
</tr>
