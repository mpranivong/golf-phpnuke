<?php get_header(); ?>
	<div id="main">
		<div class="singlecolumn">
		<?php if(have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
		<?php $title = the_title( '' , '' , false ); if(!$title) { $title = the_date('','','',false); } ?>
			<h1><?php echo $title; ?></h1>
			<div class="content clearfix"><?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'wappos') . '</p>'); ?></div>
		   	<div class="link_pages"><?php wp_link_pages(); ?></div>
			<?php wp_link_pages(); ?>
		</div><!--// post -->
		<?php comments_template( '', true ); ?>
		<?php endwhile; ?>
		<?php else : ?>
		<?php endif; ?>
		</div><!--// maincolumn -->
	</div><!--// main -->
	<?php get_sidebar('right'); ?>
<?php get_footer(); ?>