Found Posts: <?php echo $staff->found_posts; ?><div class="table-responsive">
	<table class="table table-condensed table-striped table-hover">
		<?php if (isset($atts['with_headers']) && $atts['with_headers'] !== false) { ?>
			<thead>
			<th>Staff</th>
			<th>Job Title</th>
			</thead>
		<?php } ?>
		<tbody>
		<?php

            while ($staff->have_posts()) {
                $staff->the_post(); ?>
				<tr>
					<td>
						<a href="<?php the_permalink(); ?>">
							<span
								class="staff-title"><?php echo get_post_meta(get_the_ID(), \CranleighSchool\CranleighPeople\Metaboxes::fieldID('full_title'), true); ?></span>
						</a>
						<span
							class="qualifications"><?php echo get_post_meta(get_the_ID(), \CranleighSchool\CranleighPeople\Metaboxes::fieldID('qualifications'), true); ?></span>
					</td>
					<td><?php echo get_post_meta(get_the_ID(), \CranleighSchool\CranleighPeople\Metaboxes::fieldID('leadjobtitle'), true); ?></td>

				</tr>
			<?php
            }
            wp_reset_postdata();
            wp_reset_query();
        ?>
		</tbody>
	</table>
</div>
