<?php

########################################################################
# PHP-Nuke Block: Golf Members                                         #
#                                                                      #
# Copyright (c) 2005 by M. Pranivong (tourney@dfwlga.com)              #
#                                                                      #
# 2005-09-12 Derived from existing modules                             #
########################################################################
# This program is free software. You can redistribute it and/or modify #
# it under the terms of the GNU General Public License as published by #
# the Free Software Foundation; either version 2 of the License.       # 
######################################################################## 

if (eregi("block-Golf_Members.php", $_SERVER['PHP_SELF'])) {
    Header("Location: index.php");
    die();
}

require_once("./modules/Golf/golf.class.php");

global $user, $prefix, $db, $user_prefix, $member_titles;

$content = '<table cellpadding="0" cellspacing="0" width="100%"><tr><td colspan="2"><i>active member first</i></td><td>HCP</td></tr>';

//$sql = 'select player_member_title, player_handicap_temp, u.username, u.user_id, 
//	UNIX_TIMESTAMP(player_member_date) player_member_date_unix from '.
//	$user_prefix.'_users u left join '.$user_prefix.'_golf_players gp on gp.user_id=u.user_id 
//	where player_member order by player_handicap_temp';
$date_today = time();
$date_year_ago = mktime(0,0,0,date('m'), date('d'), date('Y')-1);
$sql = 'select player_member_title, player_handicap_temp, u.username, u.user_id, 
	UNIX_TIMESTAMP(player_member_date) player_member_date_unix,
	UNIX_TIMESTAMP(player_member_renew_date) player_member_renew_date_unix,
	UNIX_TIMESTAMP(MAX(gr.round_date)) round_date_unix,
	player_member_length
	from '.$user_prefix.'_users u 
	left join '.$user_prefix.'_golf_players gp on gp.user_id=u.user_id 
	left join '.$user_prefix.'_golf_rounds gr on gr.user_id=u.user_id
	where player_member 
	group by u.user_id order by player_handicap_temp';
	
$inactive_content = ''; $active_player = false;
//echo '<pre>'.$sql.'</pre>';
$result = $db->sql_query($sql);
if ($db->sql_numrows($result)) {
	$count = 0; $active_count=0;
	while ($row = $db->sql_fetchrow($result)) {
		$content_temp = '';
		$count++;
		if ($row['player_member_renew_date_unix']) {
			$renew_date = $row['player_member_renew_date_unix'];
		} else {
			$renew_date = $row['player_member_date_unix'];
		}
		$date_membership_expire = mktime(0,0,0,date('m', $renew_date),date('d', $renew_date), date('Y', $renew_date)+$row['player_member_length']);
		$active_player = false;
		if ($date_membership_expire >= $date_today) {
			$active_count++;
			$active_player = true;
			if ($active_count==11) {
				$content .= '</table><br>';
				$content .= "<marquee behavior='scroll' direction='up' height='80px' scrollamount='1' scrolldelay='10' onMouseOver='this.stop()' onMouseOut='this.start()'>";
				$content .= '<table cellpadding="1" cellspacing="0" width="100%">';
			}
		}
		$days_ago = (time() - $row['player_member_date_unix'])/60/60/24;
		
		$content_temp = '
		<tr><td>';
		$content_temp .= '<span '.($row['player_member_title']?'style="font-weight:bold;"><image src="images/ur-author.gif"/>&nbsp;':'>').'<span '.($active_player?'':' style="font-style: italic;"').'>'.ui_form_safe(ucwords($row['username'])).'</span></span>'.
		($row['player_member_title']?'<br>'.$member_titles[$row['player_member_title']]:'').($days_ago<60?'<image src="modules/Web_Links/images/newgreen.gif"/>':'').
		'</td>';
		
		$content_temp .= '<td>';
		
		$player_photo_file = 'modules/Golf/photos/members/'.strtolower($row['username']).'.jpg';
		$player_photo_file_full = 'modules/Golf/photos/members/full_'.strtolower($row['username']).'.jpg';
		if (!file_exists($player_photo_file_full)) {
			$player_photo_file_full = $player_photo_file;
		}
		if (!file_exists($player_photo_file)) {
			$player_photo_file = 'modules/Golf/photos/members/imageholder.jpg';
			$player_photo_file_full = $player_photo_file;
		}
		if (file_exists($player_photo_file)) {
			$content_temp .= '	
			<a href="'.$player_photo_file_full.'" rel="gb_imageset[member]" title="'.strtoupper($row['username']).' - '.sprintf('%.1f',$row['player_handicap_temp']).' HCP'.($row['player_member_title']?' - '.$member_titles[$row['player_member_title']]:'').'">
		  	<img height="20" src="'.$player_photo_file.'" border=0/>
			</a';
		}
		
		$content_temp .= '</td>';
		
		$content_temp .= '<td><a href="/modules.php?name=Golf&op=players_scores&user_id='.$row['user_id'].'">'.sprintf('%.1f',$row['player_handicap_temp']).'</td></tr>';
		
		if ($active_player) {
			$content .= $content_temp;
		} else {
			$inactive_content .= $content_temp;
		}
	}
	if (strlen($inactive_content)) {
		$content .= '<tr><td colspan="2"><i>inactive members</td></tr>';
		$content .= $inactive_content;
	}
	if ($active_count >= 11) {
		$content .= "</table></marquee>".'<table cellpadding="1" cellspacing="0" width="100%">';
	}
	$content .= '<tr><td colspan="2"><hr>'.$active_count.' active, '.$count.' total</td></tr>';
	$content .= '<tr><td colspan="2">Track your scores <a href="/modules.php?name=Golf&op=players">here</a>.</td></tr>';
}

$content .= '</table>';
?>