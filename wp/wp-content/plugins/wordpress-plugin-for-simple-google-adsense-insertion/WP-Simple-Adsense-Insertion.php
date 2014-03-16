<?php
/*
Plugin Name: WP Simple Adsense Insertion
Version: v1.6
Plugin URI: http://www.tipsandtricks-hq.com/
Author: Tips and Tricks HQ, Ruhul Amin
Author URI: http://www.tipsandtricks-hq.com/
Description: A simple Wordpress plugin to insert Google Adsense ads into posts, pages, sidebars (anywhere on your site).
License: GPLv2
*/

$wp_simple_ad_insert_version = 1.6;

function show_ad_camp_1() {
	$ad_camp_encoded_value_1 = get_option( 'wp_ad_camp_1_code' );
	$ad_camp_decoded_value_1 = html_entity_decode( $ad_camp_encoded_value_1, ENT_COMPAT );

	if ( !empty( $ad_camp_decoded_value_1 ) ) {
		$output .= " $ad_camp_decoded_value_1";
	}
	return $output;
}
add_shortcode( 'wp_ad_camp_1', 'show_ad_camp_1' );

function show_ad_camp_2() {
	$ad_camp_encoded_value_2 = get_option( 'wp_ad_camp_2_code' );
	$ad_camp_decoded_value_2 = html_entity_decode( $ad_camp_encoded_value_2, ENT_COMPAT );

	if ( !empty( $ad_camp_decoded_value_2 ) ) {
		$output .= " $ad_camp_decoded_value_2";
	}
	return $output;
}
add_shortcode( 'wp_ad_camp_2', 'show_ad_camp_2' );

function show_ad_camp_3() {
	$ad_camp_encoded_value_3 = get_option( 'wp_ad_camp_3_code' );
	$ad_camp_decoded_value_3 = html_entity_decode( $ad_camp_encoded_value_3, ENT_COMPAT );

	if ( !empty( $ad_camp_decoded_value_3 ) ) {
		$output .= " $ad_camp_decoded_value_3";
	}
	return $output;
}
add_shortcode( 'wp_ad_camp_3', 'show_ad_camp_3' );

function show_ad_camp_4() {
	$ad_camp_encoded_value_4 = get_option( 'wp_ad_camp_4_code' );
	$ad_camp_decoded_value_4 = html_entity_decode( $ad_camp_encoded_value_4, ENT_COMPAT );

	if ( !empty( $ad_camp_decoded_value_4 ) ) {
		$output .= " $ad_camp_decoded_value_4";
	}
	return $output;
}
add_shortcode( 'wp_ad_camp_4', 'show_ad_camp_4' );

function show_ad_camp_5() {
	$ad_camp_encoded_value_5 = get_option( 'wp_ad_camp_5_code' );
	$ad_camp_encoded_value_5 = html_entity_decode( $ad_camp_encoded_value_5, ENT_COMPAT );

	if ( !empty( $ad_camp_encoded_value_5 ) ) {
		$output .= " $ad_camp_encoded_value_5";
	}
	return $output;
}
add_shortcode( 'wp_ad_camp_5', 'show_ad_camp_5' );

/**
 * The wp_ad_camp_process function is deprecated.
 * New users should use the updated shortcode method.
 */

function wp_ad_camp_process( $content ) {
	if ( strpos( $content, "<!-- wp_ad_camp_1 -->" ) !== FALSE ) {
		$content = preg_replace( '/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content );
		$content = str_replace( '<!-- wp_ad_camp_1 -->', show_ad_camp_1(), $content );
	}
	if ( strpos( $content, "<!-- wp_ad_camp_2 -->" ) !== FALSE ) {
		$content = preg_replace( '/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content );
		$content = str_replace( '<!-- wp_ad_camp_2 -->', show_ad_camp_2(), $content );
	}
	if ( strpos( $content, "<!-- wp_ad_camp_3 -->" ) !== FALSE ) {
		$content = preg_replace( '/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content );
		$content = str_replace( '<!-- wp_ad_camp_3 -->', show_ad_camp_3(), $content );
	}
	if ( strpos( $content, "<!-- wp_ad_camp_4 -->" ) !== FALSE ) {
		$content = preg_replace( '/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content );
		$content = str_replace( '<!-- wp_ad_camp_4 -->', show_ad_camp_4(), $content );
	}
	return $content;
}

add_filter( 'the_content', 'wp_ad_camp_process' );

// Displays Simple Ad Campaign Options menu
function ad_camp_add_option_page() {
	if ( function_exists( 'add_options_page' ) ) {
		add_options_page( 'Simple Adsense Insertion', 'Adsense Insertion', 'manage_options', __FILE__, 'ad_insertion_options_page' );
	}
}

function ad_insertion_options_page() {

	global $wp_simple_ad_insert_version;

	if ( isset( $_POST['info_update'] ) ) {
		echo '<div id="message" class="updated fade"><p><strong>';

		$tmpCode1 = htmlentities( stripslashes( $_POST['wp_ad_camp_1_code'] ) , ENT_COMPAT );
		update_option( 'wp_ad_camp_1_code', $tmpCode1 );

		$tmpCode2 = htmlentities( stripslashes( $_POST['wp_ad_camp_2_code'] ) , ENT_COMPAT );
		update_option( 'wp_ad_camp_2_code', $tmpCode2 );

		$tmpCode3 = htmlentities( stripslashes( $_POST['wp_ad_camp_3_code'] ) , ENT_COMPAT );
		update_option( 'wp_ad_camp_3_code', $tmpCode3 );

		$tmpCode4 = htmlentities( stripslashes( $_POST['wp_ad_camp_4_code'] ) , ENT_COMPAT );
		update_option( 'wp_ad_camp_4_code', $tmpCode4 );

		$tmpCode5 = htmlentities( stripslashes( $_POST['wp_ad_camp_5_code'] ) , ENT_COMPAT );
		update_option( 'wp_ad_camp_5_code', $tmpCode5 );
				
		echo 'Options Updated!';
		echo '</strong></p></div>';
	}

?>

	<div class="wrap">
		<h2>Adsense Insertion Options</h2>

		<p>Use this plugin to quickly and easily insert Google Adsense to your posts or pages.</p>
		<p>For information and updates, please visit the <a href="http://www.tipsandtricks-hq.com/wordpress-plugin-for-simple-google-adsense-insertion-170" target="_blank">simple Google Adsense plugin page</a></p>
		
		<br>
		
	    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	    <input type="hidden" name="info_update" id="info_update" value="true" />

	    <fieldset class="options">
	    <table width="100%" border="0" cellspacing="0" cellpadding="6">

	    <tr valign="top"><td width="35%" align="left">
	    <strong>Adsense Ad Campaign 1 Code:</strong>
	    <br>Copy and paste your adsense to the field on the right.
	    <br>To display this ad, use the shortcode: <code>[wp_ad_camp_1]</code>
	    </td><td align="left">
	    <textarea name="wp_ad_camp_1_code" rows="6" cols="50"><?php echo get_option( 'wp_ad_camp_1_code' ); ?></textarea>
	    </td></tr>

	    <tr valign="top"><td width="35%" align="left">
	    <strong>Adsense Ad Campaign 2 Code:</strong>
	    <br>Copy and paste your adsense to the field on the right.
	    <br>To display this ad, use the shortcode: <code>[wp_ad_camp_2]</code>
	    </td><td align="left">
	    <textarea name="wp_ad_camp_2_code" rows="6" cols="50"><?php echo get_option( 'wp_ad_camp_2_code' ); ?></textarea>
	    </td>
	    </tr>

	    <tr valign="top"><td width="35%" align="left">
	    <strong>Adsense Ad Campaign 3 Code:</strong>
	    <br>Copy and paste your adsense to the field on the right.
	    <br>To display this ad, use the shortcode: <code>[wp_ad_camp_3]</code>
	    </td><td align="left">
	    <textarea name="wp_ad_camp_3_code" rows="6" cols="50"><?php echo get_option( 'wp_ad_camp_3_code' ); ?></textarea>
	    </td></tr>


	    <tr valign="top"><td width="35%" align="left">
	    <strong>Adsense Ad Campaign 4 Code:</strong>
	    <br>Copy and paste your adsense to the field on the right.
	    <br>To display this ad, use the shortcode: <code>[wp_ad_camp_4]</code>
	    </td><td align="left">
	    <textarea name="wp_ad_camp_4_code" rows="6" cols="50"><?php echo get_option( 'wp_ad_camp_4_code' ); ?></textarea>
	    </td></tr>

	    <tr valign="top"><td width="35%" align="left">
	    <strong>Adsense Ad Campaign 5 Code:</strong>
	    <br>Copy and paste your adsense to the field on the right.
	    <br>To display this ad, use the shortcode: <code>[wp_ad_camp_5]</code>
	    </td><td align="left">
	    <textarea name="wp_ad_camp_5_code" rows="6" cols="50"><?php echo get_option( 'wp_ad_camp_5_code' ); ?></textarea>
	    </td></tr>
	    
	    </table>
	    </fieldset>

	    <div class="submit">
	        <input type="submit" class="button-primary" name="info_update" value="Update options" />
	    </div>

	    </form>
	</div>
<?php
}

// Insert the ad_camp_add_option_page in the 'admin_menu'
add_action( 'admin_menu', 'ad_camp_add_option_page' );

add_filter('the_content', 'do_shortcode');
if (!is_admin())
{add_filter('widget_text', 'do_shortcode');}
add_filter('the_excerpt', 'do_shortcode');
