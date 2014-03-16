<div class="clearit">
	<form method="get" class="searchform" name="searchform" action="<?php echo home_url(); ?>/">
		<div>
			<input type="text" name="s" class="s" value="<?php echo get_search_query(); ?>"  /> 
			<input type="image" class="sb" src="<?php echo get_template_directory_uri(); ?>/images/search_button.png" />
		</div>
	</form>
</div>