<?php
/*
Plugin Name: Drop Cap Shortcode
Version: 1.0
Plugin URI: http://ekakurniawan.com/
Author: Eka Kurniawan
Author URI: http://ekakurniawan.com/
Description: This plugin give you freedom to insert dropcap or not. Just change your first letters into a shortcode. Example: [T]his is my paragraph with a drop cap. The first letter 'T' will turn into a drop cap.
*/

/* create multiple drop caps function from A to Z and quote */
function A_dc_func() {$text = '<span class="dropcap">A</span>'; return $text;}
add_shortcode( 'A', 'A_dc_func' );

function B_dc_func() {$text = '<span class="dropcap">B</span>'; return $text;}
add_shortcode( 'B', 'B_dc_func' );

function C_dc_func() {$text = '<span class="dropcap">C</span>'; return $text;}
add_shortcode( 'C', 'C_dc_func' );

function D_dc_func() {$text = '<span class="dropcap">D</span>'; return $text;}
add_shortcode( 'D', 'D_dc_func' );

function E_dc_func() {$text = '<span class="dropcap">E</span>'; return $text;}
add_shortcode( 'E', 'E_dc_func' );

function F_dc_func() {$text = '<span class="dropcap">F</span>'; return $text;}
add_shortcode( 'F', 'F_dc_func' );

function G_dc_func() {$text = '<span class="dropcap">G</span>'; return $text;}
add_shortcode( 'G', 'G_dc_func' );

function H_dc_func() {$text = '<span class="dropcap">H</span>'; return $text;}
add_shortcode( 'H', 'H_dc_func' );

function I_dc_func() {$text = '<span class="dropcap">I</span>'; return $text;}
add_shortcode( 'I', 'I_dc_func' );

function J_dc_func() {$text = '<span class="dropcap">J</span>'; return $text;}
add_shortcode( 'J', 'J_dc_func' );

function K_dc_func() {$text = '<span class="dropcap">K</span>'; return $text;}
add_shortcode( 'K', 'K_dc_func' );

function L_dc_func() {$text = '<span class="dropcap">L</span>'; return $text;}
add_shortcode( 'L', 'L_dc_func' );

function M_dc_func() {$text = '<span class="dropcap">M</span>'; return $text;}
add_shortcode( 'M', 'M_dc_func' );

function N_dc_func() {$text = '<span class="dropcap">N</span>'; return $text;}
add_shortcode( 'N', 'N_dc_func' );

function O_dc_func() {$text = '<span class="dropcap">O</span>'; return $text;}
add_shortcode( 'O', 'O_dc_func' );

function P_dc_func() {$text = '<span class="dropcap">P</span>'; return $text;}
add_shortcode( 'P', 'P_dc_func' );

function Q_dc_func() {$text = '<span class="dropcap">Q</span>'; return $text;}
add_shortcode( 'Q', 'Q_dc_func' );

function R_dc_func() {$text = '<span class="dropcap">R</span>'; return $text;}
add_shortcode( 'R', 'R_dc_func' );

function S_dc_func() {$text = '<span class="dropcap">S</span>'; return $text;}
add_shortcode( 'S', 'S_dc_func' );

function T_dc_func() {$text = '<span class="dropcap">T</span>'; return $text;}
add_shortcode( 'T', 'T_dc_func' );

function U_dc_func() {$text = '<span class="dropcap">U</span>'; return $text;}
add_shortcode( 'U', 'U_dc_func' );

function V_dc_func() {$text = '<span class="dropcap">V</span>'; return $text;}
add_shortcode( 'V', 'V_dc_func' );

function W_dc_func() {$text = '<span class="dropcap">W</span>'; return $text;}
add_shortcode( 'W', 'W_dc_func' );

function X_dc_func() {$text = '<span class="dropcap">X</span>'; return $text;}
add_shortcode( 'X', 'X_dc_func' );

function Y_dc_func() {$text = '<span class="dropcap">Y</span>'; return $text;}
add_shortcode( 'Y', 'Y_dc_func' );

function Z_dc_func() {$text = '<span class="dropcap">Z</span>'; return $text;}
add_shortcode( 'Z', 'Z_dc_func' );

function quote_dc_func() {$text = '<span class="dropcap">&quot;</span>'; return $text;}
add_shortcode( 'quote', 'quote_dc_func' );


// We need some CSS to position the dropcap, change this section to fit with your design
function dropcap_css() {

	echo "
	<style type='text/css'>
	span.dropcap {
		display: inline;
		float: left;
		margin: 0;
		padding: .25em .08em 0 0;
		#padding: 0.25em 0.08em 0.2em 0.00em;/* override for Microsoft Internet Explorer browsers*/
		_padding: 0.25em 0.08em 0.4em 0.00em; /* override for IE browsers 6.0 and older */	
		font-size: 3.2em;
		line-height: .4em;
		text-transform: capitalize;
		color: #c30;
		font-family: Georgia, Times New Romans, Trebuchet MS, Lucida Grande;
	}
	</style>
	";
}

add_action('wp_head', 'dropcap_css');

?>