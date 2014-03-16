<?php get_header(); ?>
	<div id="main" class="clearfix">
		<div class="maincolumn">
		<?php if(have_posts()) : ?>

		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 		<?php /* If this is a category archive */ if (is_category()) { ?>
			<h1><?php single_cat_title(); ?></h1>
 		<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
			<h1><?php _e('Posts Tagged &#8216;', 'wappos'); ?><?php single_tag_title(); ?>&#8217;</h1>
		<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
			<h1><?php printf( __( 'Daily Archives: <span>%s</span>', 'wappos' ), get_the_date() ); ?></h1>
		<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<h1><?php printf( __( 'Monthly Archives: <span>%s</span>', 'wappos' ), get_the_date( __('F Y', 'wappos') ) ); ?></h1>
		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<h1><?php printf( __( 'Yearly Archives: <span>%s</span>', 'wappos' ), get_the_date( __('Y', 'wappos') ) ); ?></h1>
		<?php /* If this is an author archive */ } elseif (is_author()) { ?>
			<h1><?php printf( __( 'Author Archives: %s', 'wappos' ), "<span class='vcard'><a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a></span>" ); ?></h1>
 	  	<?php /* If this is a paged archive */ } elseif (is_paged()) { ?>
			<h1><?php _e('Blog Archives', 'wappos'); ?></h1>
		<?php } ?>

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