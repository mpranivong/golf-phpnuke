<?php
$ua = $_SERVER['HTTP_USER_AGENT'];
if (!(ereg("Windows",$ua) && ereg("MSIE",$ua)) || ereg("MSIE 7",$ua)) {
     echo '<' . '?' . 'xml version="1.0" encoding="' . get_option('blog_charset') .'"?>' . "\n";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes( 'xhtml' ); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title><?php wp_title('',true); ?><?php if(wp_title('',false)) { ?> | <?php } ?><?php bloginfo('name'); ?></title> 
<meta name="description" content="<?php bloginfo('description'); ?>" /> 
<?php
if ( is_singular() && get_option( 'thread_comments' ) )
wp_enqueue_script( 'comment-reply' );
?>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link href="<?php echo get_stylesheet_directory_uri(); ?>/style.css" rel="stylesheet" type="text/css" media="all" />
<!--[if IE 6]>
	<script>
		DD_belatedPNG.fix('img');
	</script>
<![endif]-->
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="top_menu_div" class="menu-container clearfix">
	<div id="top_content">
		<span id="sitedesc"><?php bloginfo('description'); ?></span>
		<?php wp_nav_menu( array( 'container_class' => 'menu-top', 'theme_location' => 'top_menu', 'fallback_cb' => ''  ) ); ?></div>
	</div>
<div id="header">
	<div id="logo">
		<?php
			$h_img = get_header_image();
			if($h_img) {
		?>
		<a href="<?php echo home_url(); ?>"><img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="<?php bloginfo('name'); ?>" /></a>
		<?php } else { ?>
		<div id="sitename"><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a></div>
		<?php } ?>
	</div><!--// logo -->

	<div id="header_search">
		<?php get_search_form(); ?>
	</div>

</div><!--// header -->
<div id="header_menu_div" class="menu-container clearfix"><?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'header_menu' ) ); ?></div>
<div id="wrap" class="clearfix">