<?php get_header(); ?>
	<div id="main">
		<div class="maincolumn">
		<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
			<h1><?php _e('Sorry! We coudn\'t find it.', 'wappos'); ?></h1>
			<div class="content">
			<p><?php _e('We are sorry, the object you requested was not found on this server.', 'wappos'); ?></p>
			</div><!--// content -->
			</div><!--// post -->
		</div><!--// maincolumn -->
		<?php get_sidebar('left'); ?>
	</div><!--// main -->
	<?php get_sidebar('right'); ?>
<?php get_footer(); ?>