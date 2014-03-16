<div id="left">
	<?php if ( is_active_sidebar( 'left_sidebar') ) : ?>
		<?php dynamic_sidebar('left_sidebar') ?>
	<?php else : ?>
	<div class="sidecolumn">
		<h3>Categories</h3>
		<ul><?php wp_list_categories('title_li='); ?></ul>
	</div>
	<?php endif; ?>
</div><!--// left -->
