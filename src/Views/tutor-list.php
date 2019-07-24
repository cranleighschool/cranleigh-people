<div class="row">
	<?php
		while ($staff->have_posts()): $staff->the_post(); ?>
			<div class="col-sm-<?php echo $class; ?>">
				<?php echo \CranleighSchool\CranleighPeople\Shortcodes::small(get_the_ID()); ?>
			</div>
		<?php endwhile; ?>
</div>
