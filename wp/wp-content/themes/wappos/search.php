<?php get_header(); ?>
	<div id="main" class="clearfix">
		<div class="maincolumn">
<?php if ( have_posts() ) : ?>
			<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'wappos' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		<?php while (have_posts()) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
			<h2 class="post_title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<div class="content clearfix">
				<?php
					if( get_post_thumbnail_id() ) {
						the_post_thumbnail();
						the_excerpt();
					} else {
						the_content( __('more &raquo;', 'wappos') );
					}
				?>
			</div>
		    <div class="link_pages"><?php wp_link_pages(); ?></div>
		</div><!--// post -->
		<?php endwhile; ?>
		<div class="nav-interior clearfix">
			<div class="nav-previous"><?php next_posts_link( __('&laquo; Older Entries', 'wappos') ) ?></div>
			<div class="nav-next"><?php previous_posts_link( __('Newer Entries &raquo;', 'wappos') ) ?></div>
		</div>
		<?php else : ?>
		<?php endif; ?>
		</div><!--// maincolumn -->
		<?php get_sidebar('left'); ?>
	</div><!--// main -->
	<?php get_sidebar('right'); ?>
<?php get_footer(); ?>