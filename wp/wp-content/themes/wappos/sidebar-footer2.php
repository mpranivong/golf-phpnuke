<div class="footer_menu">
	<?php if ( is_active_sidebar( 'footer_2') ) : ?>
		<?php dynamic_sidebar('footer_2') ?>
	<?php else : ?>
		<h3>Links</h3>
		<ul><?php wp_list_bookmarks('title_before=<h4>&title_after=</h4>'); ?></ul>
	<?php endif; ?>
</div>