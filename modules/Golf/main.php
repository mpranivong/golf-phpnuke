<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2005 by M. Pranivong (tourney@dfwlga.com)               */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* GOLF MODULE                                                          */
/* 2005.09.08  Created                                                  */
/************************************************************************/
if (!eregi("modules.php", $_SERVER['PHP_SELF'])) {
	die ("You can't access this file directly...");
}

require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

require_once("./modules/Golf/golf.class.php");

global $db, $user_prefix;
$user_info = getusrinfo($user);
// ******** load config *************
$golf_config = array();
$golf_config['advertising_email'];
$golf_config['membership_fee'] = 40.00;
$golf_config['tournament_show_year'] = 0;	// all
$golf_config['home_html'] = 'Set this message in Golf admin';
$sql = "SELECT * FROM ".$user_prefix."_golf_config";
$result = $db->sql_query($sql);
if ($db->sql_numrows($result)) {
	$row = $db->sql_fetchrow($result);
	$golf_config['membership_fee'] = $row['config_member_fee'];
	$golf_config['tournament_show_year'] = $row['config_tournament_year'];
	$golf_config['tournament_email'] = $row['config_tournament_email'];
	$golf_config['paypal_email'] = $row['config_paypal_email'];
	$golf_config['paypal_return_url'] = $row['config_paypal_return_url'];
	$golf_config['mail_address'] = $row['config_mail_address'];
	$golf_config['home_html'] = $row['config_home_html'];
	$golf_config['tournament_rules'] = $row['config_tournament_rules_html'];
	$golf_config['photo_url'] = $row['config_photo_url'];
}
if ($_GET['tournament_year']) {
	$golf_config['tournament_show_year'] = $_GET['tournament_year'];
}
// ******************************

include ('header.php');

// load my module files
$my_module_names = explode('_', $op);
$my_module_menu = $my_module_names[0].'_menu';
$my_module_file = './modules/Golf/'.$my_module_names[0].'.php';
if (!file_exists($my_module_file)) {
	$my_module_file = './modules/Golf/home.php';
}
include( $my_module_file );

$index = 1; // flag to show right column blocks
OpenTable();
if (!$_GET['no_header']) {
	nav();
}
if (strlen($op)) {
	if (function_exists($my_module_menu)) {
		eval($my_module_menu.'();');
	}
	if (function_exists($op)) {
		eval($op.'();');
	} else {
		echo '<center>'.$op.' coming soon.</center>';
	}
} else {
	main();
}

CloseTable();

?>


<script language="javascript">
function showimage(file) {
	alert(player_photo.src);
	player_photo.src = file;
	player_photo.top = 500;
	player_photo.left = 500;
	return false;
}

</script>
<script type="text/javascript">
function calcScore(name, par) {
  if (document.scores["score_is_par"].checked == true) {
    document.scores[name].value = par - (-document.scores[name].value);
  }
}
function calcTots() {
  var in_tot = 0;
  var out_tot = 0;
  var tot = 0;
  var i;

  namePrefix = 'score_value';

  //out
  for(i=1; i<10; i++) {
    out_tot = out_tot + Number(document.scores[namePrefix + '_' + i].value);
  }
  document.getElementById("out_" + namePrefix).innerHTML = out_tot;

  //in
  if(size == 18) {
    for(i=10; i<19; i++) {
      in_tot = in_tot + Number(document.scores[namePrefix + '_' + i].value);
    }
    document.getElementById("in_" + namePrefix).innerHTML = in_tot;

    //tot
    document.getElementById("total_" + namePrefix).innerHTML = in_tot + out_tot;
  }
  else {
    document.getElementById("total_" + namePrefix).innerHTML = out_tot;
  }

}
</script>
<img id="player_photo" name="player_photo" style="position:absolute;top:-1000px;left:-1000px;width:200px;height:200px">
</img>

<?php
include ('footer.php');

function nav() {
	global $admin, $sitename, $user_info, $golf_config;
	$s = '<table align="center">';

	//$s .= '<tr><td><table align="center"><tr><td><b><font size="6">' .
    //   $sitename . '</b></font></td></tr></table></td></tr>';
	//$s .= '<tr><td>
	//		<table align="center" valign="middle"><tr>'.
	//			'<td><a href="'.$_SERVER['REQUEST_URI'].'&no_header=1" title="printer friendly page"><img src="/modules/Golf/images/icon_print.gif" border="0"/></a></td>
	//			</tr>
	//		</table></td></tr>';
	$s .= '<tr><td><table><tr>';
	/*$s .= '
        <td>[<a href="/modules.php?name=Golf&op=home">Home</a>]</td>
		<td>[<a href="/modules.php?name=Golf&op=tournaments">Tournaments</a>]</td>
		<td>[<a href="/modules.php?name=Golf&op=players">Players/Stats</a>]</td>';
	if ($user_id=get_user_id()) {
		$s .= '<td>[<a href="/modules.php?name=Golf&op=players_scores&user_id='.$user_id.'">My Scores</a>]</td>';
	}
	$s .= '<td>[<a href="/modules.php?name=Golf&op=courses">Golf Courses</a>]</td>';
	if ($admin) {
		$s .= '<td>[<a href="/modules.php?name=Golf&op=admin">Admin</a>]</td>';
	}
	*/
	$s .= '</tr></table></td></tr';
	$s .= '</table>';
	$s .= '<hr>';
	echo $s;
}
function main() {
	home();
}
function cal_handicap_diff($score, $rating=71, $slope=124) {
	if ($slope > 0) {
		return (($score - $rating) * 113) / $slope;
	}
}
function get_version() {
	global $user_prefix, $db;

	$existing_version = 0;
    $sql = "SELECT * FROM ".$user_prefix."_golf_config";
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$row = $db->sql_fetchrow($result);
		$existing_version = $row['config_version'];
	}
	return $existing_version;
}
function get_user_id() {
	global $admin, $user_info;

	$uid = $user_info['user_id'];
	if (!$uid) $uid=0;

	return $uid;
}
?>

