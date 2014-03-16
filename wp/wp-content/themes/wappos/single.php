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
		</div><!--// post -->
		<div class="contentmeta">
			<ul>
				<li><?php the_date(); ?> <?php the_time(); ?></li>
				<li><?php _e('Author', 'wappos'); ?>: <?php the_author_posts_link(); ?><?php edit_post_link('Edit', ' | ', ''); ?></li>
				<?php if( !is_attachment()) { ?>
				<li><?php _e('Categories', 'wappos'); ?>: <?php the_category(', '); ?></li>
				<?php } ?>
				<li><?php the_tags(); ?></li>
			</ul>
		</div><!--// contentmeta -->
		<div id="nav-below" class="navigation clearfix">
			<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&laquo;', 'Previous post link', 'wappos' ) . '</span> %title' ); ?></div>
			<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&raquo;', 'Next post link', 'wappos' ) . '</span>' ); ?></div>
		</div><!-- #nav-below -->
		<?php comments_template( '', true ); ?>
		<?php endwhile; ?>
		<?php else : ?>
		<?php endif; ?>
		</div><!--// maincolumn -->
	</div><!--// main -->
	<?php get_sidebar('right'); ?>
<?php get_footer(); ?>