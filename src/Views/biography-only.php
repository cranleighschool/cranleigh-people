<section class="biography-pullout" id="<?php echo \CranleighSchool\CranleighPeople\Shortcodes::sanitize_title_to_id($card_title); ?>">
	<div class="pull-out">

		<?php echo \CranleighSchool\CranleighPeople\Shortcodes::get_first_paragraph($post_id); ?>

		<?php if (strlen(\CranleighSchool\CranleighPeople\Shortcodes::get_second_paragraph($post_id)) > 1) : ?>
			<p class="read-more">
				<a href="#<?php echo \CranleighSchool\CranleighPeople\Shortcodes::sanitize_title_to_id($card_title); ?>-bio"
				   data-toggle="collapse" class="cranleigh-hide-readmore-link" aria-controls="person-bio"
				   aria-expanded="false">Read more…</a>
			</p>
		<?php endif; ?>

		<div id="<?php echo \CranleighSchool\CranleighPeople\Shortcodes::sanitize_title_to_id($card_title); ?>-bio" class="collapse"
			 aria-expanded="false">
			<?php echo \CranleighSchool\CranleighPeople\Shortcodes::get_second_paragraph($post_id); ?>
		</div>

	</div>
</section>
