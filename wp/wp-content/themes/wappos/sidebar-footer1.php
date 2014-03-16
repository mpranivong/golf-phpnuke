<div class="footer_menu">
	<?php if ( is_active_sidebar( 'footer_1') ) : ?>
		<?php dynamic_sidebar('footer_1') ?>
	<?php else : ?>
		<h3>Tags</h3>
		<p><?php wp_tag_cloud(); ?></p>
	<?php endif; ?>
</div>