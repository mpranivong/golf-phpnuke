<div id="right">
	<?php if ( is_active_sidebar( 'right_sidebar') ) : ?>
		<?php dynamic_sidebar('right_sidebar') ?>
	<?php else : ?>
	<div class="sidecolumn">
		<h3>Recent Posts</h3>	
		<ul><?php wp_get_archives('type=postbypost&limit=10'); ?></ul>
		<h3>Archives</h3>
		<ul><?php wp_get_archives(); ?></ul>
	</div>
	<?php endif; ?>
</div><!--// right -->
