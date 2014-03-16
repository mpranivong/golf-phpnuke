<div class="footer_menu">
	<?php if ( is_active_sidebar( 'footer_4') ) : ?>
		<?php dynamic_sidebar('footer_4') ?>
	<?php else : ?>
		<h3>Meta</h3>
		<ul>
			<?php wp_register('<li>', '</li>'); ?> 
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>">RSS</a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>">Comments RSS</a></li>
		</ul>
	<?php endif; ?>
</div>