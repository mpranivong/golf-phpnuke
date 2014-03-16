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

function admin() {
	global $admin, $db, $user_prefix, $mod_version;
	if (!$admin) { echo 'Access denied, admin only!'; return; }

    $sql = "SELECT * FROM ".$user_prefix."_golf_config";
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$row = $db->sql_fetchrow($result);
		$existing_version = $row['config_version'];
	
		$s = '<form name="courses" action="/modules.php?name=Golf&op=admin_edit" method="POST"/>';
		$s .= '<table align="center">';
		$s .= '<tr><td><table align="center"><tr><td><b>Adminstration Menu</b></td></tr></table></td></tr>';
	
		$s .= '<tr><td><table cellpadding=2 cellpadding=4 cellspacing=0><tr>';
		$s .= '<td align="right">Module Version:</td><td>'. $existing_version .'&nbsp;&nbsp;';
		if ($existing_version == 0) {
			$s .= '<a href="/modules.php?name=Golf&op=admin_upgrade">install</a>';
		} else if ($mod_version <> $existing_version) {
			$s .= '<a href="/modules.php?name=Golf&op=admin_upgrade">upgrade</a> to ' . $mod_version;
		} else {
			$s .= '&nbsp;';
		}
		$s .= '</td></tr>';
		$s .= '<tr><td align="right">Membership Fee:</td><td><input type="text" name="config_member_fee" value="'.sprintf('%.2f', $row['config_member_fee']).'" size="6" maxlength="10"/></td></tr>';
		$s .= '<tr><td align="right">Tournament Year:</td><td><input type="text" name="config_tournament_year" value="'.$row['config_tournament_year'].'" size="6" maxlength="4"/> <font size="1">year to display in tournament block, ex: 2005</font></td></tr>';
		$s .= '<tr><td align="right">Tournament Email:</td><td valign="top"><input type="text" name="config_tournament_email" value="'.ui_form_safe($row['config_tournament_email']).'" size="60" maxlength="64"/> <font size="1">for sign ups to contact you about tournaments</font></td></tr>';
		$s .= '<tr><td align="right">PayPal Payment Email:</td><td valign="top"><input type="text" name="config_paypal_email" value="'.ui_form_safe($row['config_paypal_email']).'" size="60" maxlength="64"/> <font size="1">email used to receive payment from paypal, optional</font></td></tr>';
		$s .= '<tr><td align="right">PayPal Return URL:</td><td valign="top"><input type="text" name="config_paypal_return_url" value="'.ui_form_safe($row['config_paypal_return_url']).'" size="60" maxlength="100"/> <font size="1">return to this url after user makes a paypal payment, optional</font></td></tr>';
		$s .= '<tr><td align="right" valign="top">Mailing Address:</td><td valign="top"><textarea name="config_mail_address" cols="50" rows="4">'.ui_form_safe($row['config_mail_address']).'</textarea> <font size="1">address for users to send checks to your club, optional</font></td></tr>';
		$s .= '<tr><td align="right" valign="top">Photos:</td><td><input type="text" name="config_photo_url" value="'.ui_form_safe($row['config_photo_url']).'" size="60" maxlength="100"/> <font size="1">use external photo sharing website such as shuttle fly and put url here</font></td>';
		$s .= '<tr><td align="right" valign="top">Home Page HTML:</td><td valign="top"><textarea name="config_home_html" cols="120" rows="20">'.ui_form_safe($row['config_home_html']).'</textarea></td></tr>';
		$s .= '<tr><td align="right" valign="top">Mission HTML:</td><td valign="top"><textarea name="config_mission_html" cols="120" rows="20">'.ui_form_safe($row['config_mission_html']).'</textarea></td></tr>';
		$s .= '<tr><td align="right" valign="top">Membership Info HTML:</td><td valign="top"><textarea name="config_membership_info_html" cols="120" rows="20">'.ui_form_safe($row['config_membership_info_html']).'</textarea></td></tr>';
		$s .= '<tr><td align="right" valign="top">Tournament Rules HTML:</td><td valign="top"><textarea name="config_tournament_rules_html" cols="120" rows="20">'.ui_form_safe($row['config_tournament_rules_html']).'</textarea></td></tr>';
		$s .= '<tr><td align="right" valign="top">Advertisers Agreement HTML:</td><td valign="top"><textarea name="config_ad_terms_html" cols="120" rows="20">'.ui_form_safe($row['config_ad_terms_html']).'</textarea></td></tr>';
		$s .= '<tr><td align="right" valign="top">Bylaws HTML:</td><td valign="top"><textarea name="config_bylaws_html" cols="120" rows="20">'.ui_form_safe($row['config_bylaws_html']).'</textarea></td></tr>';
		$s .= '<tr><td align="right" valign="top">Past Officers HTML:</td><td valign="top"><textarea name="config_past_officers_html" cols="120" rows="20">'.ui_form_safe($row['config_past_officers_html']).'</textarea></td></tr>';
		$s .= '<tr><td align="right" valign="top">Handicap Info HTML:</td><td valign="top"><textarea name="config_handicap_html" cols="120" rows="20">'.ui_form_safe($row['config_handicap_html']).'</textarea></td></tr>';
		$s .= '<tr><td align="right">&nbsp;</td><td><input type="submit" value="update"/></td></tr>';
		$s .= '</table></td></tr>';
		
		$s .= '</table>';
		echo $s;
	}
}
function admin_edit() {
	global $user, $admin, $db, $user_prefix, $user_info;
	if(!$admin) { echo '<span class="error">Restricted area, admin only!</span>'; return; }

	$return_url = trim($_POST['config_paypal_return_url']);
	if (strlen($return_url)) {
		if (substr(strtolower($return_url),0,7) != 'http://') {
			$return_url = 'http://'.$return_url;
		}
	}
	
	$sql = 'UPDATE '.$user_prefix.'_golf_config SET 
		config_member_fee="'.addslashes($_POST['config_member_fee']).'",
		config_tournament_year="'.FixQuotes($_POST['config_tournament_year']).'",
		config_paypal_email="'.FixQuotes($_POST['config_paypal_email']).'",
		config_paypal_return_url="'.FixQuotes($return_url).'",
		config_mail_address="'.FixQuotes($_POST['config_mail_address']).'",
		config_home_html="'.FixQuotes($_POST['config_home_html']).'",
		config_membership_info_html="'.FixQuotes($_POST['config_membership_info_html']).'",
		config_tournament_rules_html="'.FixQuotes($_POST['config_tournament_rules_html']).'",
		config_ad_terms_html="'.FixQuotes($_POST['config_ad_terms_html']).'",
		config_bylaws_html="'.FixQuotes($_POST['config_bylaws_html']).'",
		config_mission_html="'.FixQuotes($_POST['config_mission_html']).'",
		config_past_officers_html="'.FixQuotes($_POST['config_past_officers_html']).'",
		config_tournament_email="'.FixQuotes($_POST['config_tournament_email']).'",
		config_photo_url="'.FixQuotes($_POST['config_photo_url']).'"
		limit 1';

	$result = $db->sql_query($sql);
	if(!$result) {
		echo ""._ERROR."<br> $sql<br>";
		exit();
	}
	header('Location: /modules.php?name=Golf&op=admin');
	
}
function admin_upgrade() {
	global $admin, $db, $user_prefix, $mod_version;
	if(!$admin) { echo '<span class="error">Restricted area, admin only!</span>'; return; }
	
	$existing_version = get_version();
	
	$success = true;
	
	switch ($existing_version) {
	case '0':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_config (
		   config_id int(11) NOT NULL auto_increment,
		   version varchar(10) NOT NULL DEFAULT "0",
		   PRIMARY KEY (config_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'INSERT INTO '.$user_prefix.'_golf_config VALUES(0,"' . $mod_version .'")';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.1':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_tournaments (
		   tournament_id int(11) NOT NULL auto_increment,
		   tournament_name varchar(100) NOT NULL DEFAULT "Event Name",
		   tournament_date datetime NOT NULL,
		   tournament_cost_member decimal(10,4) NOT NULL DEFAULT 0,
		   tournament_cost_nonmember decimal(10,4) NOT NULL DEFAULT 0,
		   tournament_deadline datetime NOT NULL,
		   course_id int NOT NULL DEFAULT 0,
		   course_teebox_id tinyint NOT NULL DEFAULT 0,
		   tournament_format varchar(64) NOT NULL DEFAULT "",
		   tournament_partner_require tinyint NOT NULL DEFAULT 0,
		   tournament_mulligan varchar(64) NOT NULL DEFAULT "",
		   tournament_side_contests varchar(100) NOT NULL DEFAULT "",
		   tournament_prizes varchar(100) NOT NULL DEFAULT "",
		   tournament_note varchar(100) NOT NULL DEFAULT "",
		   PRIMARY KEY (tournament_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	
	case '0.1.1':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_courses (
		   course_id int(11) NOT NULL auto_increment,
		   course_name varchar(100) NOT NULL DEFAULT "Course Name",
		   course_phone varchar(64) NOT NULL DEFAULT "",
		   course_url varchar(64) NOT NULL DEFAULT "",
		   PRIMARY KEY (course_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_active tinyint NOT NULL DEFAULT 1 AFTER tournament_prizes';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	
	case '0.1.2':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_courses ADD course_address varchar(64) NOT NULL DEFAULT "" AFTER course_name';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_courses ADD course_city varchar(32) NOT NULL DEFAULT "" AFTER course_address';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_courses ADD course_state varchar(2) NOT NULL DEFAULT "" AFTER course_city';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	
	case '0.1.3':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_courses ADD course_note varchar(100) NOT NULL DEFAULT "" AFTER course_url';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.1.4':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_results varchar(255) NOT NULL DEFAULT "" AFTER tournament_prizes';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.1.5':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_course_teeboxes (
		   teebox_id int(11) NOT NULL auto_increment,
		   teebox_name varchar(32) NOT NULL DEFAULT "",
		   teebox_slope decimal(6,2) NOT NULL DEFAULT 124,
		   teebox_rating decimal(6,2) NOT NULL DEFAULT 70,
		   course_id int(11) NOT NULL DEFAULT 0,
		   PRIMARY KEY (teebox_id),
		   KEY (course_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.1.6':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments CHANGE course_teebox_id teebox_id tinyint NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.1.8':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_course_teeboxes CHANGE teebox_slope teebox_slope tinyint NOT NULL DEFAULT 124';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.1.9':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_course_teeboxes ADD teebox_added_by int(11) NOT NULL DEFAULT 0 AFTER teebox_rating';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_courses ADD course_added_by int(11) NOT NULL DEFAULT 0 AFTER course_note';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.0':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments CHANGE teebox_id teebox_id smallint NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.1':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_tournament_signups (
		   signup_id int(11) NOT NULL auto_increment,
		   signup_name varchar(32) NOT NULL DEFAULT "",
		   signup_email varchar(64) NOT NULL DEFAULT "",
		   signup_phone varchar(32) NOT NULL DEFAULT "",
		   signup_handicap decimal(6,2) NOT NULL DEFAULT 0,
		   signup_time datetime NOT NULL,
		   signup_group tinyint NOT NULL DEFAULT 0,
		   tournament_id int(11) NOT NULL DEFAULT 0,
		   PRIMARY KEY (signup_id),
		   KEY (tournament_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.2':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD signup_paid decimal(6,2) NOT NULL DEFAULT 0 AFTER signup_group';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD signup_member tinyint NOT NULL DEFAULT 0 AFTER signup_group';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.3':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments CHANGE tournament_results tournament_results TEXT NOT NULL';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.5':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_course_teeboxes CHANGE teebox_slope teebox_slope smallint NOT NULL DEFAULT 124';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.6':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments CHANGE tournament_note tournament_note TEXT NOT NULL';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.7':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config CHANGE version config_version varchar(10) NOT NULL DEFAULT "0"';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD signup_flight varchar(1) NOT NULL DEFAULT "A" AFTER signup_handicap';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.8':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_member_fee decimal(6,2) NOT NULL DEFAULT "50.00" AFTER config_version';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.2.9':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_tournament_year smallint NOT NULL DEFAULT 2005 AFTER config_version';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.0':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_flights tinyint NOT NULL DEFAULT 0 AFTER tournament_format';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.1':
		// score is for backward compatibilities when each hole score were not kept
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD signup_score smallint NOT NULL DEFAULT 0 AFTER signup_paid';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.2':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_scores (
		   score_id int(11) NOT NULL auto_increment,
		   score_value tinyint NOT NULL DEFAULT 0,
		   score_fairway tinyint NOT NULL DEFAULT 0,
		   score_drive_distance smallint NOT NULL DEFAULT 0,
		   score_gir tinyint NOT NULL DEFAULT 0,
		   score_putts tinyint NOT NULL DEFAULT 0,
		   user_id int(11) NOT NULL DEFAULT 0,
		   hole_id int(11) NOT NULL DEFAULT 0,
		   tournament_id int(11) NOT NULL DEFAULT 0,
		   PRIMARY KEY (score_id),
		   KEY (tournament_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD user_id int(11) NOT NULL DEFAULT 0 AFTER tournament_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_mail_address varchar(200) NOT NULL DEFAULT "edit this in admin" AFTER config_tournament_year';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_paypal_email varchar(100) NOT NULL DEFAULT "" AFTER config_mail_address';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_paypal_return_url varchar(100) NOT NULL DEFAULT "" AFTER config_paypal_email';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_tournament_items (
		   item_id int(11) NOT NULL auto_increment,
		   item_value tinyint NOT NULL DEFAULT 0,
		   item_group tinyint NOT NULL DEFAULT 0,
		   item_type tinyint NOT NULL DEFAULT 0,
		   tournament_id int(11) NOT NULL DEFAULT 0,
		   PRIMARY KEY (item_id),
		   KEY (tournament_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_long_drives tinyint NOT NULL DEFAULT 0 AFTER tournament_flights';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_closest_to_the_pins tinyint NOT NULL DEFAULT 0 AFTER tournament_long_drives';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_mulligans tinyint NOT NULL DEFAULT 0 AFTER tournament_format';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.3':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_tournament_flights (
		   flight_id int(11) NOT NULL auto_increment,
		   flight_name varchar(32) NOT NULL DEFAULT "",
		   flight_max_hcp tinyint NOT NULL DEFAULT 0,
		   teebox_id int(11) NOT NULL DEFAULT 0,
		   tournament_id int(11) NOT NULL DEFAULT 0,
		   PRIMARY KEY (flight_id),
		   KEY (tournament_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_tournament_side_contest_holes (
		   hole_id int(11) NOT NULL auto_increment,
		   hole_type tinyint NOT NULL DEFAULT 0,
		   flight_id int(11) NOT NULL DEFAULT 0,
		   tournament_id int(11) NOT NULL DEFAULT 0,
		   winner_signup_id int(11) NOT NULL DEFAULT 0,
		   winner_user_id int(11) NOT NULL DEFAULT 0,
		   PRIMARY KEY (hole_id),
		   KEY (tournament_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}

		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments DROP tournament_long_drives';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments DROP tournament_closest_to_the_pins';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments DROP tournament_flights';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.4':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_flights CHANGE flight_name flight_name varchar(32) NOT NULL DEFAULT ""';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.5':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_holes (
		   hole_id int(11) NOT NULL auto_increment,
		   hole_number tinyint NOT NULL DEFAULT 0,
		   hole_distance smallint NOT NULL DEFAULT 0,
		   hole_handicap tinyint NOT NULL DEFAULT 0,
		   teebox_id int(11) NOT NULL DEFAULT 0,
		   PRIMARY KEY (hole_id),
		   KEY (teebox_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.6':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_scores DROP INDEX tournament_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_scores DROP tournament_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_scores DROP user_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_scores ADD score_info_id int(11) NOT NULL DEFAULT 0 AFTER hole_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_score_info (
		   score_info_id int(11) NOT NULL auto_increment,
		   score_info_date datetime NOT NULL,
		   user_id int(11) NOT NULL DEFAULT 0,
		   enter_by_user_id int(11) NOT NULL DEFAULT 0,
		   tournament_id int(11) NOT NULL DEFAULT 0,
		   teebox_id int(11) NOT NULL DEFAULT 0,
		   PRIMARY KEY (score_info_id),
		   KEY (user_id),
		   KEY (tournament_id) 
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.7':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_score_info ADD signup_id int(11) NOT NULL DEFAULT 0 AFTER user_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_holes ADD hole_par tinyint NOT NULL DEFAULT 0 AFTER hole_number';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.8':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_score_info CHANGE enter_by_user_id score_info_added_by int(11) NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.3.9':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_scores ADD KEY (score_info_id)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.4.0':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_score_info RENAME '.$user_prefix.'_golf_rounds';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds CHANGE score_info_id round_id int';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds CHANGE score_info_date round_date datetime';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds CHANGE score_info_added_by round_added_by int';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_scores DROP INDEX score_info_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_scores CHANGE score_info_id round_id int';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_scores ADD KEY (round_id)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.4.1':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds CHANGE round_id round_id int(11) NOT NULL auto_increment';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds CHANGE round_date round_date datetime NOT NULL';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds CHANGE round_added_by round_added_by int(11) NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.4.3':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD signup_withdraw tinyint NOT NULL DEFAULT 0 AFTER signup_group';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.4.4':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_score smallint NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.4.5':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups DROP signup_score';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_score_temp smallint NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.4.7':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_flights CHANGE flight_max_hcp flight_max_hcp decimal(4,1) NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.4.8':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_side_contest_holes RENAME '.$user_prefix.'_golf_tournament_side_contests';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_side_contests CHANGE hole_id contest_id int(11) NOT NULL auto_increment';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_side_contests CHANGE hole_type contest_type tinyint NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_side_contests ADD hole_id int(11) NOT NULL DEFAULT 0 AFTER tournament_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.4.9':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_side_contests ADD contest_note VARCHAR(10) NOT NULL DEFAULT "" AFTER winner_signup_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_side_contests DROP winner_user_id';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.5.0':
		$sql = '
		CREATE TABLE '. $user_prefix . '_golf_players (
		   player_id int(11) NOT NULL auto_increment,
		   player_member tinyint NOT NULL DEFAULT 0,
		   player_member_date datetime NOT NULL,
		   player_member_year tinyint NOT NULL DEFAULT 0,
		   player_member_title tinyint NOT NULL DEFAULT 0,
		   player_ghin VARCHAR(16) NOT NULL DEFAULT "",
		   player_handicap_start decimal(4,1) NOT NULL DEFAULT 0,
		   player_handicap_temp decimal(4,1) NOT NULL DEFAULT 0,
		   player_note TEXT(1000) NOT NULL DEFAULT "",
		   
		   user_id int(11) NOT NULL DEFAULT 0,
		   
		   PRIMARY KEY (player_id),
		   KEY (user_id)
		)';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.5.1':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_players CHANGE player_member_year player_member_length tinyint NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.5.3':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_flights ADD flight_net_awards tinyint NOT NULL DEFAULT 1';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_flights ADD flight_gross_awards tinyint NOT NULL DEFAULT 1';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.5.4':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD signup_net_standing tinyint NOT NULL DEFAULT 0 AFTER signup_paid';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD signup_gross_standing tinyint NOT NULL DEFAULT 0 AFTER signup_paid';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.5.5':
		$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET signup_flight=""';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups CHANGE signup_flight flight_id INT NOT NULL DEFAULT 0';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.5.6':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_nonmember_winner tinyint NOT NULL DEFAULT 0 AFTER tournament_format';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.5.7':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournament_signups ADD signup_ghin VARCHAR(16) NOT NULL DEFAULT "" AFTER signup_handicap';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_home_html TEXT';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.5.9':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_max_players smallint NOT NULL DEFAULT 0 AFTER tournament_deadline';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_tournament_email VARCHAR(64) NOT NULL DEFAULT "" AFTER config_tournament_year';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.6.0':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_score_diff DECIMAL(4,1) NOT NULL DEFAULT 0 AFTER round_score_temp';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.6.1':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_fairways TINYINT NOT NULL DEFAULT 0 AFTER round_score_diff';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_girs TINYINT NOT NULL DEFAULT 0 AFTER round_score_diff';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_putts TINYINT NOT NULL DEFAULT 0 AFTER round_score_diff';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.6.2':
		$sql = 'SELECT gr.round_id, SUM(score_fairway) fairways, SUM(score_gir) girs, SUM(score_putts) putts 
			FROM '.$user_prefix.'_golf_rounds gr 
			LEFT JOIN '.$user_prefix.'_golf_scores gs ON gs.round_id=gr.round_id 
			GROUP BY round_id';
		$result = $db->sql_query($sql);
		//echo '<pre>'.$sql.'</pre>';
		if ($db->sql_numrows($result)) {
			while ($row = $db->sql_fetchrow($result)) {
				if ($row['fairways'] || $row['girs'] || $row['putts']) {
					$sql = 'UPDATE '.$user_prefix.'_golf_rounds SET round_fairways='.$row['fairways'].', round_girs='.$row['girs'].',
						round_putts='.$row['putts'].' where round_id='.$row['round_id'].' limit 1';
					$db->sql_query($sql);
					//echo '<pre>'.$sql.'</pre>';
				}
			}
		}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_mission_html TEXT';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_ad_terms_html TEXT';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_tournament_rules_html TEXT';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_bylaws_html TEXT';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.6.3':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_membership_info_html TEXT';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.6.4':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_past_officers_html TEXT';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.6.5':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_handicap_html TEXT';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.6.9':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_par5 DECIMAL(4,1) NOT NULL DEFAULT 0 AFTER round_putts';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_par4 DECIMAL(4,1) NOT NULL DEFAULT 0 AFTER round_putts';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_par3 DECIMAL(4,1) NOT NULL DEFAULT 0 AFTER round_putts';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}

		$sql = 'select gr.round_id, sum(if(hole_par = 3, score_value, 0)) round_par3,
			sum(if(hole_par = 3, 1, 0)) round_par3_count,
			sum(if(hole_par = 4, score_value, 0)) round_par4,
			sum(if(hole_par = 4, 1, 0)) round_par4_count,
			sum(if(hole_par = 5, score_value, 0)) round_par5,
			sum(if(hole_par = 5, 1, 0)) round_par5_count
			from '.$user_prefix.'_users u 
			left join '.$user_prefix.'_golf_rounds gr on gr.user_id=u.user_id
			left join '.$user_prefix.'_golf_scores gs on gs.round_id=gr.round_id
			left join '.$user_prefix.'_golf_holes gh on gh.hole_id=gs.hole_id
			group by gr.round_id';
		$result = $db->sql_query($sql);
		//echo '<pre>'.$sql.'</pre>';
		if ($db->sql_numrows($result)) {
			$gross = '<td><table cellpadding="1" cellspacing="0">';
			while ($row = $db->sql_fetchrow($result)) {
				if ($row['round_par3_count'] && $row['round_par4_count'] && $row['round_par5_count'])  {
					$sql = 'update '.$user_prefix.'_golf_rounds set round_par3="'.($row['round_par3']/$row['round_par3_count']).
					'", round_par4="'.($row['round_par4']/$row['round_par4_count']).'", round_par5="'.($row['round_par5']/$row['round_par5_count']).
					'" where round_id='.$row['round_id'];
					$db->sql_query($sql);
				}
			}
		}
	case '0.7.0':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_rounds ADD round_ryder_points DECIMAL(5,1) NOT NULL DEFAULT 0 AFTER round_fairways';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.7.1':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_config ADD config_photo_url VARCHAR(100) NOT NULL DEFAULT ""';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}
	case '0.7.2':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_players ADD player_member_renew_date DATETIME NOT NULL DEFAULT 0 AFTER player_member_length';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}	
	default:
	case '0.7.3':
		$sql = 'select gr.round_id, sum(if(hole_par = 3, score_value, 0)) round_par3,
			sum(if(hole_par = 3, 1, 0)) round_par3_count,
			sum(if(hole_par = 4, score_value, 0)) round_par4,
			sum(if(hole_par = 4, 1, 0)) round_par4_count,
			sum(if(hole_par = 5, score_value, 0)) round_par5,
			sum(if(hole_par = 5, 1, 0)) round_par5_count
			from '.$user_prefix.'_users u 
			left join '.$user_prefix.'_golf_rounds gr on gr.user_id=u.user_id
			left join '.$user_prefix.'_golf_scores gs on gs.round_id=gr.round_id
			left join '.$user_prefix.'_golf_holes gh on gh.hole_id=gs.hole_id
			group by gr.round_id';
		$result = $db->sql_query($sql);
		//echo '<pre>'.$sql.'</pre>';
		if ($db->sql_numrows($result)) {
			$gross = '<td><table cellpadding="1" cellspacing="0">';
			while ($row = $db->sql_fetchrow($result)) {
				if ($row['round_par3_count'] && $row['round_par4_count'] && $row['round_par5_count'])  {
					$sql = 'update '.$user_prefix.'_golf_rounds set round_par3="'.($row['round_par3']/$row['round_par3_count']).
					'", round_par4="'.($row['round_par4']/$row['round_par4_count']).'", round_par5="'.($row['round_par5']/$row['round_par5_count']).
					'" where round_id='.$row['round_id'];
					$db->sql_query($sql);
				}
			}
		}
	case '0.7.4':
		$sql = 'ALTER TABLE '.$user_prefix.'_golf_tournaments ADD tournament_photo_url VARCHAR(200) NOT NULL DEFAULT ""';
		if (!$db->sql_query($sql)) {echo $sql; $success = false;}	
	break;
	}
	
	if ($success) {
		$sql = 'UPDATE '.$user_prefix.'_golf_config SET config_version="' . $mod_version .'"';
		$result = $db->sql_query($sql);
		header('Location: /modules.php?name=Golf&op=admin');
	} else {
		echo "Failed.";
	}
}

?>
