</div><!--// wrap -->
<div id="footer">
	<div id="footer_menu_div" class="clearfix">
		<?php get_sidebar('footer1'); ?>
		<?php get_sidebar('footer2'); ?>
		<?php get_sidebar('footer3'); ?>
		<?php get_sidebar('footer4'); ?>
	</div>
	<div id="copyright">
	<a href="<?php bloginfo('rss2_url'); ?>" class="feed">subscribe to posts</a> or <a href="<?php bloginfo('comments_rss2_url'); ?>" class="feed">subscribe to comments</a><br />
	Powered by WordPress using the <a href="http://www.jusanya.com/wappos">Wappos Theme</a><br />
	Copyright &copy; <?php echo date('Y'); ?> <a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>. All Rights Reserved.</div>
</div><!--// footer -->
<?php wp_footer(); ?>
</body>
</html>