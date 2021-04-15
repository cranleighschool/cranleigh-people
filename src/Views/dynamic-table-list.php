<table class="table <?php
	echo $a['class']; ?>">
	<?php
	foreach ( $users as $person => $dull ) {
		$username = trim( $person );
		$staff_member = \CranleighSchool\CranleighPeople\Shortcodes\DynamicTableListShortcode::get_wp_post_from_username( $username );
		if ( $staff_member instanceof WP_Post ) {
			echo self::render( 'table-row', compact( 'staff_member', 'first_column', 'last_column' ) );
		}
	}
	?>
</table>
