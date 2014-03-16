<div class="footer_menu">
	<?php if ( is_active_sidebar( 'footer_3') ) : ?>
		<?php dynamic_sidebar('footer_3') ?>
	<?php else : ?>
		<h3>Authors</h3>
		<ul><?php wp_list_authors(); ?></ul>
	<?php endif; ?>
</div>