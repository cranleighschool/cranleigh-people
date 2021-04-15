<?php

?>
<div class="card landscape">
	<div class="row">
		<div class="col-xs-4">
			<div class="card-image">
				<a href="<?php the_permalink(); ?>">
					<?php
						\CranleighSchool\CranleighPeople\View::the_post_thumbnail();
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
