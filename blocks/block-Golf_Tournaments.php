<?php

########################################################################
# PHP-Nuke Block: Golf Tournament                                      #
#                                                                      #
# Copyright (c) 2005 by M. Pranivong (tourney@dfwlga.com)              #
#                                                                      #
# 2005-09-12 Derived from existing modules                             #
########################################################################
# This program is free software. You can redistribute it and/or modify #
# it under the terms of the GNU General Public License as published by #
# the Free Software Foundation; either version 2 of the License.       # 
######################################################################## 

if (eregi("block-Golf_Tournaments.php", $_SERVER['PHP_SELF'])) {
    Header("Location: index.php");
    die();
}

global $db, $user_prefix;

$content = '<table cellpadding="1" cellspacing="0" width="100%">';

$tournament_year = date('Y');
$sql = "SELECT * FROM ".$user_prefix."_golf_config";
$result = $db->sql_query($sql);
if ($db->sql_numrows($result)) {
	$row = $db->sql_fetchrow($result);
	$tournament_year = $row['config_tournament_year'];
}

$sql = 'select tournament_id, tournament_name, UNIX_TIMESTAMP(tournament_date) as tournament_date 
from '.$user_prefix.'_golf_tournaments gt where year(tournament_date) = '.$tournament_year;
$sql .= ' order by tournament_date';	// oldest first, req by richard s

$future_count = 0;
$older = '';

$result = $db->sql_query($sql);
if ($db->sql_numrows($result)) {
	$count = 0;
	$today = time();
	$content .= '<table>';
	while ($row = $db->sql_fetchrow($result)) {
		$tourney_info = '';
		$count++;
		if ($row['tournament_date'] > $today) {
			$future_count++;
			if ($future_count == 3) { //if ($count==3) {
				$content .= '</table><br>';
				$content .= "<marquee behavior='scroll' direction='up' height='60px' scrollamount='1' scrolldelay='10' onMouseOver='this.stop()' onMouseOut='this.start()'>";
				$content .= '<table cellpadding="1" cellspacing="0" width="100%">';
			}
		}
		
		$tourney_info = '<tr><td><a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$row['tournament_id'].'">'.(strlen($row['tournament_name'])>19?substr($row['tournament_name'],0, 16).'..':$row['tournament_name']).'</a></td>';
		$tourney_info .= '<td valign="top">';
		if ($today >= $row['tournament_date']) $tourney_info .= '<s>';
		$tourney_info .= date('m/d',$row['tournament_date']);
		if ($today >= $row['tournament_date']) $tourney_info .= '</s>';
		$tourney_info .= '</td></tr>';
		
		if ($row['tournament_date'] > $today) {
			$content .= $tourney_info;
		} else {
			$older .= $tourney_info;
		}
	}
	$content .= $older;
	if ($future_count >= 3) { //if ($count >= 3) {
		$content .= "</table></marquee>".'<br><table cellpadding="1" cellspacing="0" width="100%">';
	}
}
$content .= '<tr><td colspan="2" align="center" nowrap> See <a href="modules.php?name=Golf&op=tournaments_rules">RULES</a> for details.</td></tr>';
$content .= "</table>";
?>