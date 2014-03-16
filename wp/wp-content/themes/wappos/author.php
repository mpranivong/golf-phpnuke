<?php get_header(); ?>
	<div id="main" class="clearfix">
		<div class="maincolumn">
		<?php
if ( have_posts() ) :
	the_post();
?>
		<h1 class="page-title author"><?php printf( __( 'Author Archives: %s', 'wappos' ), "<span class='vcard'><a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a></span>" ); ?></h1>

		<?php if ( get_the_author_meta( 'description' ) ) : ?>
			<div id="entry-author-info" class="clearfix">
				<div id="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), 62 ); ?>
				</div><!-- #author-avatar -->
				<div id="author-description">
					<?php the_author_meta( 'description' ); ?>
				</div><!-- #author-description	-->
			</div><!-- #entry-author-info -->
		<?php endif; ?>
		<?php rewind_posts(); ?>
		<?php while (have_posts()) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
		<?php $title = the_title( '' , '' , false ); if(!$title) { $title = the_date('','','',false); } ?>
			<h2 class="post_title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php echo $title; ?>"><?php echo $title; ?></a></h2>
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