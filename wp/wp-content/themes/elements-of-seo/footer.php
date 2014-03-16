</div>
<!-- end wrap -->



<!-- begin footer -->

<div style="clear:both;"></div>
<div style="clear:both;"></div>

<div id="footer">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(3) ) : ?>
<?php endif; ?>
</div>
<div id="footer">
	<p>Copyright 2010-<?= date('Y')?> | CountingMiles.com</p>
</div>


<?php do_action('wp_footer'); ?>

</body>
</html>