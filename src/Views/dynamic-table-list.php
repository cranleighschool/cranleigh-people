<table class="table <?php use CranleighSchool\CranleighPeople\View;

	echo $a['class']; ?>">
	<?php
		foreach ($users as $person => $dull) {
			$username = trim($person);
			$staff_member = \CranleighSchool\CranleighPeople\Shortcodes\DynamicTableListShortcode::get_wp_post_from_username($username);
			echo self::render('table-row', compact('staff_member', 'first_column', 'last_column'));
		}
	?>
</table>