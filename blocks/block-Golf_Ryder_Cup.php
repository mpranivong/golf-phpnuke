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

if (eregi("block-Golf_Ryder_Cup.php", $_SERVER['PHP_SELF'])) {
    Header("Location: index.php");
    die();
}

require_once("./modules/Golf/golf.class.php");

global $user, $prefix, $db, $user_prefix, $member_titles;

$content = '<table cellpadding="1" cellspacing="0" width="100%">';

	$sql = 'select gr.round_id, UNIX_TIMESTAMP(gr.round_date) round_date, u.user_id, u.username, round_ryder_points, gr.tournament_id, 
		gts.signup_gross_standing, gts.signup_net_standing, gtf.flight_name
		from '.$user_prefix.'_golf_rounds gr 
		left join '.$user_prefix.'_users u on u.user_id=gr.user_id 
		left join '.$user_prefix.'_golf_tournament_signups gts on (gts.user_id=gr.user_id and gts.tournament_id=gr.tournament_id)
		left join '.$user_prefix.'_golf_tournament_flights gtf on gtf.flight_id=gts.flight_id
		where year(gr.round_date) = "'.date('Y').'" AND gr.tournament_id > 0 AND flight_name in ("A","B") AND u.user_id > 0';
	//echo '<pre>'.$sql.'</pre>';
	$result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$names = array();
		$points = array();
		while ($row = $db->sql_fetchrow($result)) {
			cal_points( date('Y'), $row['round_date'], $row['tournament_id'], $row['round_ryder_points'], $row['signup_gross_standing'], $row['signup_net_standing'], $ryder_points, $champion_points );
			if (!isset($ch_points[$row['user_id']])) $ch_points[$row['user_id']] = 0;
			foreach (@$ryder_points as $k => $v) {
				$points[$row['user_id']] += $v;
			}
			$names[$row['user_id']]=$row['username'].'('.$row['flight_name'].')';
		}
		arsort($points);
		$count = 0;
		foreach ($points as $k => $v) {
			if ($v > 0 && strlen($k) > 0) {
				$count++;
				if ($count==3) {
					$content .= '</table><br>';
					$content .= "<marquee behavior='scroll' direction='up' height='60px' scrollamount='1' scrolldelay='10' onMouseOver='this.stop()' onMouseOut='this.start()'>";
					$content .= '<table cellpadding="1" cellspacing="0" width="100%">';
				}
				$content .= '<tr><td><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($names[$k]).'</a></td><td>'.(sprintf('%.1f',$v)).'</td></tr>';
			}
		}
		if ($count >= 3) {
			$content .= "</table></marquee>".'<br><table cellpadding="1" cellspacing="0" width="100%">';
		}
	}
	$content .= '<tr><td colspan="2" align="center" nowrap> See <a href="modules.php?name=Golf&op=tournaments_rules">RULES</a> for details.</td></tr>';

$content .= '</table>';
?>