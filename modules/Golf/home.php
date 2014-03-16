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

include_once('./modules/Golf/scores.php');

function home() {
	global $admin, $db, $user_prefix, $golf_config;

	$s .= '<table align="center" border="0" width="100%">';
	$s .= '<tr><td colspan="2" align="center">
	<script type="text/javascript" src="http://static.ak.connect.facebook.com/connect.php/en_US"></script><script type="text/javascript">FB.init("4e5ee261ad2d628ef968e9d8eded3afa");</script><fb:fan profile_id="164360606733" stream="1" connections="0" logobar="1" width="600"></fb:fan><div style="font-size:8px;"><a href="http://www.facebook.com/pages/DFW-Lao-Golf/164360606733">DFW Lao Golf</a> on Facebook</div>
	</td></tr>'; 
	$s .= '<tr><td colspan="2" align="center">'.$golf_config['home_html'].'</td></tr>'; 
	$s .= '<tr><td align="center" colspan="2">'.tournament_winners(0).'</td></tr>';
	$s .= '<tr><td align="center"><table><tr><td><b>Tournaments</b></td></tr></table></td>	
		       <td align="center"><table><tr><td><b>Stats (Tournaments)</b><br>(last 12 months)</td></tr></table></td>
		  </tr>';

	$s .= '<tr><td></td>';
	$s .=  '<td valign="top" align="center" rowspan="3">'.full_stats().'</td>
		   </tr>';
	$s .= '<tr><td align="center" valign="top"><b>Recent Leaderboard (Last 30)</b></td>
		  </tr>';
	$s .= '<tr>
			<td valign="top" align="center">'.scores_show(40, 2, true).'</td>';
	$s .= '</tr>';
	$s .= '</table>';
	echo $s;

}
function recent_tournaments($detail_level=5) {
	global $admin, $db, $user_prefix, $golf_config;
	
	$date_format[1] = 'D, M j, Y g:ia';
	$date_format[2] = 'D, M j, Y';
	$name_length = 0;
	switch ($detail_level) {
	case 1:
		$date_format[1] = 'M d';
		$date_format[2] = 'M d';
		$name_length = 12;
	break;
	case 2:
		$date_format[1] = 'M d g:ia';
		$date_format[2] = 'M d';
		$name_length = 30;
	break;
	case 3:
		$name_length = 30;
	break;
	case 4:
		$name_length = 40;
	break;
	default:
	break;
	}

	$sql = 'select tournament_results, tournament_id, tournament_name, UNIX_TIMESTAMP(tournament_date) as tournament_date, 
		course_name, course_url, UNIX_TIMESTAMP(tournament_deadline) tournament_deadline 
		from '.$user_prefix.'_golf_tournaments gt 
		left join '.$user_prefix.'_golf_courses gc on gc.course_id=gt.course_id 
		left join '.$user_prefix.'_golf_course_teeboxes gct on gct.teebox_id=gt.teebox_id ';
	if ($golf_config['tournament_show_year']) {
		$sql .= 'where year(tournament_date) = '. $golf_config['tournament_show_year'];
	}
	$sql .= ' order by tournament_date DESC';
	$s = '<table border="1px solid black" cellpadding="1" cellspacing="0" align="center" valign="top">';
	if ($detail_level > 2) {
		$s .= '<tr><td><b>Tournament</b></td><td><b>Date</b></td>'.($detail_level>3?'<td><b>Sign Up</b></td>':'').($detail_level>3?'<td><b>Results</b></td>':'').($detail_level>3?'<td><b>Photos</b></td>'.($admin?'<td>&nbsp;</td><td>&nbsp;</td>':''):'').'</tr>';
	}
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$today = time();
		$check_previous = true; $is_previous = false;
		while ($row = $db->sql_fetchrow($result)) {
			if ($check_previous && $today>$tournament_date) {
				$is_previous = true;
				$check_previous = false;
			}
			$tournament_date = $row['tournament_date'];
			$tournament_deadline = $row['tournament_deadline'];
			$tournament_name = ($name_length?substr($row['tournament_name'], 0, $name_length).'...': $row['tournament_name']);
			$s .= '<tr>
			<td valign="top"><a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">'.$tournament_name.'</a></td>
			<td valign="top">'.($today>$tournament_date?'<s>':'').date($date_format[1],$tournament_date);
			if ($detail_level > 3) {
				if ($today <= $tournament_deadline) {
					$s .= '<br>(deadline '.date($date_format[2],$tournament_deadline).')';
				}
			}
			$s .= ($today>$tournament_date?'</s>':'').'</td>';
			if ($detail_level > 3) {
				$s .= '<td>'.($today<=$tournament_deadline?'<a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">sign up</a>':'&nbsp;').'</td>';
			}
			if ($detail_level > 1) {
				$result_html='&nbsp;';
				if ($today >= $tournament_date) {

					if ($is_previous) {
						$result_html = '<img src="/modules/Web_Links/images/newblue.gif"/><br>'.tournament_results($row['tournament_id'], 2);
						$is_previous = false;
					} else {
						$result_html = tournament_results($row['tournament_id'], 1);
					}
					
				}
				$s .= '<td align="center">'.$result_html .'</td>';
			}
			if ($detail_level > 3) {
				$photo_dir = getcwd() . '/modules/Golf/photos/'.$row['tournament_id'];
				$spg_file = getcwd() . '/modules/Golf/photos/sp_index.php';
				$photo_html='';
				if ($today >= $tournament_date) {
					if (file_exists($spg_file)) {
						$photo_html = 'coming soon';
						if (file_exists($photo_dir)) {
							if (is_dir($photo_dir)) {
								if ($dh = opendir($photo_dir)) {
									while (false !== ($file = readdir($dh))) {
										$file = strtolower($file);
										if (substr($file, strlen($file)-4, 4) == '.jpg') {
											$photo_html = '<a href="/modules.php?name=Golf&op=tournaments_photos&tournament_id='.$row['tournament_id'].'">photos</a>';
											break;
										}
									}
								}
							}
						}
					} else {
						$photo_html = '<a href="javascript:alert(\'See admin for details.\');">not supported</a>';
					}
				}
				$s .= '<td>'.$photo_html.'&nbsp;</td>'.
				($admin?'<td><a href="/modules.php?name=Golf&op=tournaments&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">edit</a></td>':'').
				($admin?'<td><a href="/modules.php?name=Golf&op=tournaments_delete&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">delete</a></td>':'');
			}
			$s .= '</tr>';
		}
	}
	$s .= '</table>';
	return $s;
}
function tournament_results($tournament_id, $detail_level=5) {
	global $admin, $db, $user_prefix, $golf_config;
	
	$full_result_link = '<a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$tournament_id.'&tournament_year='.$golf_config['tournament_show_year'].'">full results</a>';
	
	$max_players = 0;
	$show_scores = true;
	switch ($detail_level) {
	case 1:
		return $full_result_link;
	break;
	case 2:
		$show_scores = false;
		$max_players = 3;
	break;
	case 3:
		$show_scores = true;
		$max_players = 3;
	break;
	case 4:
		$show_scores = true;
		$max_players = 10;
	break;
	}
	$s = '<table cellpadding="1" cellspacing="0" width="100%"><tr>';
	
	// ************* GROSS ***********
	$sql = 'SELECT signup_name, username, flight_name, gtf.flight_id, flight_gross_awards, signup_gross_standing FROM '.$user_prefix.'_golf_tournament_signups gts
		LEFT JOIN '.$user_prefix.'_golf_tournament_flights gtf ON gtf.flight_id=gts.flight_id
		LEFT JOIN '.$user_prefix.'_users u ON u.user_id=gts.user_id
		WHERE gts.tournament_id='.$tournament_id.' AND flight_gross_awards ORDER BY gtf.flight_max_hcp, gts.signup_gross_standing';
	$flight_count = array();
	$result = $db->sql_query($sql);
	//echo '<pre>'.$sql.'</pre>';
	if ($db->sql_numrows($result)) {
		$gross = '<td><table cellpadding="1" cellspacing="0">';
		while ($row = $db->sql_fetchrow($result)) {
			if ($row['signup_gross_standing']) {
				if (!isset($flight_count[$row['flight_id']])) {
					$flight_count[$row['flight_id']] = 0;
					$gross .= '<tr><td colspan="2"><i><u>'.$row['flight_name'].' (gross)</i></u></td></tr>';
				}
				$flight_count[$row['flight_id']]++;
				if ($flight_count[$row['flight_id']] <= $max_players || $max_players == 0) {
					$gross .= '<tr><td><i>'.($row['signup_gross_standing']==1?'<b>':'').$row['signup_gross_standing'].'</i>:</td><td>'.($row['signup_gross_standing']==1?'<b>':'').($row['username']?$row['username']:$row['signup_name']).'</td></tr>';
				}
			}
		}
		$gross .= '</table></td>';
		$s .= $gross;
	}

	// **************** NET **********************	
	$sql = 'SELECT signup_name, username, flight_name, gtf.flight_id, flight_net_awards, signup_net_standing FROM '.$user_prefix.'_golf_tournament_signups gts
		LEFT JOIN '.$user_prefix.'_golf_tournament_flights gtf ON gtf.flight_id=gts.flight_id
		LEFT JOIN '.$user_prefix.'_users u ON u.user_id=gts.user_id
		WHERE gts.tournament_id='.$tournament_id.' AND flight_net_awards ORDER BY gtf.flight_max_hcp, gts.signup_net_standing';
	$flight_count = array();
	$result = $db->sql_query($sql);
	//echo '<pre>'.$sql.'</pre>';
	if ($db->sql_numrows($result)) {
		$gross = '<td><table cellpadding="1" cellspacing="0">';
		while ($row = $db->sql_fetchrow($result)) {
			if ($row['signup_net_standing']) {
				if (!isset($flight_count[$row['flight_id']])) {
					$flight_count[$row['flight_id']] = 0;
					$gross .= '<tr><td colspan="2"><i><u>'.$row['flight_name'].' (net)</i></u></td></tr>';
				}
				$flight_count[$row['flight_id']]++;
				if ($flight_count[$row['flight_id']] <= $max_players || $max_players == 0) {
					$gross .= '<tr><td><i>'.($row['signup_net_standing']==1?'<b>':'').$row['signup_net_standing'].'</i>:</td><td>'.($row['signup_net_standing']==1?'<b>':'').($row['username']?$row['username']:$row['signup_name']).'</td></tr>';
				}
			}
		}
		$gross .= '</table></td>';
		$s .= $gross;
	}
	$s .= '</tr>
	<tr><td colspan="2">'.tournament_side_contests($tournament_id, $detail_level).'</td></tr>';
	if ($detail_level < 5) {
		$s .= '<tr><td colspan="2" align="center">'.$full_result_link.'</td></tr>';
	}
	$s .= '</table>';
	return $s;
}
function tournament_side_contests( $tournament_id, $detail_level=5 ) {
	global $admin, $db, $user_prefix, $hole_contests, $hole_contests_short;

	$show_label = true; $show_note = true; $show_hole = true; $show_flight = true;
	$full_label = true;
	switch ($detail_level) {
	case 1:
		return ''; 
	break;
	case 2:
		$full_label = false;
		$show_label = false;
		$show_note = false;
		$show_hole = false;
		$show_flight = false;
	break;
	case 3:
		$show_label = false;
	break;
	case 4:
	break;
	}
	
	$s .= '<table cellpadding="1" cellspacing="0">';
	
	$sql = 'select gtsc.contest_note, contest_type, u.username, gts.signup_name, hole_number, hole_par, flight_name 
	from '.$user_prefix.'_golf_tournament_side_contests gtsc 
	left join '.$user_prefix.'_golf_tournament_signups gts on gts.signup_id=gtsc.winner_signup_id 
	left join '.$user_prefix.'_users u on u.user_id=gts.user_id 
	left join '.$user_prefix.'_golf_holes gh on gh.hole_id=gtsc.hole_id
	left join '.$user_prefix.'_golf_tournament_flights gtf on gtf.flight_id=gtsc.flight_id
	where gtsc.tournament_id='.$tournament_id.' order by gtsc.hole_id, gtsc.contest_id';
	//echo '<pre>'.$sql.'</pre>';
	$result = $db->sql_query($sql);
	if ($db->sql_numrows($result) ) {
		$h_row = '<tr>';
		$c_row = '<tr>';
		$f_row = '<tr>';
		$w_row = '<tr>';
		$n_row = '<tr>';
		if ($show_label) {
			$s .= '<tr><td>Contest</td><td>Hole</td><td>Flight</td><td>Flight</td><td>Winner</td><td>Note</td></tr>';
		}
		$h = 0;
		while ($row = $db->sql_fetchrow($result)) {
			$h++;
			$s .= '<tr>';
			if ($show_hole) {
				$s .= '<td>'.ui_form_safe('#'.$row['hole_number'].' - Par '. $row['hole_par']).'</td>';
			}
			$s .= '<td align="right"><i>'.ui_form_safe($full_label?$hole_contests[$row['contest_type']]:$hole_contests_short[$row['contest_type']]).'('.ui_form_safe($row['flight_name']?$row['flight_name']:'all').')</i>:</td>';
			if ($show_flight) {
				$s .= '<td>'.ui_form_safe($row['flight_name']?$row['flight_name']:'all').'</td>';
			}
			$s .= '<td>'.ui_form_safe($row['username']?$row['username']:$row['signup_name']).'</td>';
			if ($show_note) {
				$s .= '<td>'.ui_form_safe($row['contest_note']).'</td>';
			}
			$s .= '</tr>';
		}
	}
	$s .= '</table>';
	return $s;
	
}
function cup_points( $detail_level=5, $year=0 ) {
	global $db, $user_prefix;
	
	$s = '<table border="1px solid black" cellpadding="1" cellspacing="0" align="center" valign="top" width="1%">';
	$sql = 'select gr.round_id, UNIX_TIMESTAMP(gr.round_date) round_date, u.user_id, u.username, round_ryder_points, gr.tournament_id, gts.signup_gross_standing, gts.signup_net_standing
		from '.$user_prefix.'_golf_rounds gr 
		left join '.$user_prefix.'_users u on u.user_id=gr.user_id 
		left join '.$user_prefix.'_golf_tournament_signups gts on (gts.user_id=gr.user_id and gts.tournament_id=gr.tournament_id)
		where year(gr.round_date) = "'.date('Y').'" AND gr.tournament_id > 0';
	//echo '<pre>'.$sql.'</pre>';
	$result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$points = array(); $players_name = array();
		$ch_points = array();
		while ($row = $db->sql_fetchrow($result)) {
			cal_points( date('Y'), $row['round_date'], $row['tournament_id'], $row['round_ryder_points'], $row['signup_gross_standing'], $row['signup_net_standing'], $ryder_points, $champion_points );
			if (!isset($points[$row['user_id']])) $points[$row['user_id']] = 0;
			if (!isset($ch_points[$row['user_id']])) $ch_points[$row['user_id']] = 0;
			foreach (@$ryder_points as $k => $v) {
				$points[$row['user_id']] += $v;
			}
			foreach (@$champion_points as $k => $v) {
				$ch_points[$row['user_id']] += $v;
			}
			$names[$row['user_id']]=$row['username'];
		}
		arsort($ch_points);
		$s .= '<tr><td width="1%" align="center"><b>Player</b></td><td width="1%" align="center"><b>Champions Cup</b></td></tr>';
		foreach ($ch_points as $k => $v) {
			if ($v > 0) {
				$s .= '<tr><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($names[$k]).'</a></td><td align="center">'.(sprintf('%.1f',$v)).'</td></tr>';
			}
		}
		arsort($points);
		$s .= '<tr><td width="1%" align="center"><b>Player</b></td><td width="1%" align="center"><b>Ryder Cup</b></td></tr>';
		foreach ($points as $k => $v) {
			if ($v > 0) {
				$s .= '<tr><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($names[$k]).'</a></td><td align="center">'.(sprintf('%.1f',$v)).'</td></tr>';
			}
		}
	}
	$s .= '<tr><td colspan="2" align="center" nowrap>Points awarded based on tournament participations and results</td></tr>';
	$s .= '<tr><td colspan="2" align="center" nowrap> See <a href="http://www.dfwlga.com/modules.php?name=Golf&op=tournaments_rules">RULES</a> for details.</td></tr>';
	$s .= '</table>';
	
	
	return $s;
}
function full_stats( ) {
	global $db, $user_prefix;
	$date_last_12_month = mktime(0, 0, 0, date('m')-12, 1, date('Y'));
	$s = '<table border="1px solid black" cellpadding="1" cellspacing="0" align="center" valign="top">';
	
	$sql = 'select u.user_id, u.username, sum(sc.score_drive_distance) total_distance, sum(if(score_drive_distance > 0, 1, 0)) distance_count,
		sum(sc.score_value) total_score, count(hole_id) score_count, 
		sum(sc.score_fairway) total_fairway, sum(sc.score_gir) total_gir 
		from '.$user_prefix.'_users u 
		left join '.$user_prefix.'_golf_rounds gr on gr.user_id=u.user_id 
		left join '.$user_prefix.'_golf_scores sc on sc.round_id=gr.round_id 
		where gr.round_date >= "'.date('Y-m-d H:i:s',$date_last_12_month).'" AND gr.tournament_id > 0 
		group by user_id';
	//echo '<pre>'.$sql.'</pre>';
	$result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$avg_distances = array(); $players_name = array(); $fairways = array(); $girs = array(); $putts = array();
		$par3s = array(); $par4s = array(); $par5s = array();
		while ($row = $db->sql_fetchrow($result)) {
			$avg_distances[$row['user_id']] = 0;
			//$fairways[$row['user_id']] = 0;
			//$girs[$row['user_id']] = 0;
			if ($row['distance_count'] > 0) {
				$avg_distances[$row['user_id']] = $row['total_distance']/$row['distance_count'];
			}
			//if ($row['score_count'] > 0) {
			//	$fairways[$row['user_id']] = $row['total_fairway']/$row['score_count'];
			//	$girs[$row['user_id']] = $row['total_gir']/$row['score_count'];
			//}
			$players_name[$row['user_id']] = $row['username'];
		}
		$sql = 'select u.user_id, u.username, sum(gr.round_score_temp) score_total, count(gr.round_id) score_rounds, 
		sum(gr.round_fairways) fairway_total, sum(if(gr.round_fairways > 0, 1, 0)) fairway_rounds, 
		sum(gr.round_girs) gir_total, sum(if(round_girs > 0, 1, 0)) gir_rounds, 
		sum(gr.round_putts) putt_total, sum(if(round_putts > 0, 1, 0)) putt_rounds,
		sum(gr.round_par3) par3_total,
		sum(gr.round_par4) par4_total,
		sum(gr.round_par5) par5_total,
		sum(if(round_par3 > 0, 1, 0)) par3_rounds,
		sum(if(round_par4 > 0, 1, 0)) par4_rounds,
		sum(if(round_par5 > 0, 1, 0)) par5_rounds
		from '.$user_prefix.'_users u 
		left join '.$user_prefix.'_golf_players gp on gp.user_id=u.user_id 
		left join '.$user_prefix.'_golf_rounds gr on gr.user_id=u.user_id 
		where player_member and gr.round_date >= "'.date('Y-m-d H:i:s',$date_last_12_month).'" AND gr.tournament_id > 0
		group by user_id ';
		//echo '<pre>'.$sql.'</pre>';
		$result = $db->sql_query($sql);
		if ($db->sql_numrows($result)) {
			$avg_round_scores = array();
			while ($row = $db->sql_fetchrow($result)) {
				$avg_round_scores[$row['user_id']] = 0;
				if ($row['score_rounds'] > 1) {
					//echo $row['username'].'/'.$row['score_rounds'].'<br>';
					$avg_round_scores[$row['user_id']] = $row['score_total']/$row['score_rounds'];
				}
				if ($row['fairway_rounds']) {
					$fairways[$row['user_id']] = $row['fairway_total']/($row['fairway_rounds'] * 14);
				}
				if ($row['gir_rounds']) {
					$girs[$row['user_id']] = $row['gir_total']/($row['gir_rounds']*18);
				}
				if ($row['putt_rounds']) {
					$putts[$row['user_id']] = $row['putt_total']/($row['putt_rounds']*18);
				}
				if ($row['par3_rounds'] > 2) {
					$par3s[$row['user_id']] = $row['par3_total']/$row['par3_rounds'];
				}
				if ($row['par4_rounds'] > 2) {
					$par4s[$row['user_id']] = $row['par4_total']/$row['par4_rounds'];
				}
				if ($row['par5_rounds'] > 2) {
					$par5s[$row['user_id']] = $row['par5_total']/$row['par5_rounds'];
				}
				$players_name[$row['user_id']] = $row['username'];
			}
		}
		
		// score avg
		$s .= '<tr><td width="1%">&nbsp;</td><td align="center" colspan="2"><b>Avg Round (2+)</b></td></tr>';
		asort($avg_round_scores);
		$count = 0;
		foreach ($avg_round_scores as $k => $v) {
			if ($v > 0) {
				$count++;
				$s .= '<tr><td width="1%">'.$count.'</td><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($players_name[$k]).'</a></td><td align="center">'.(sprintf('%.1f',$v)).'</td></tr>';
				if ($count > 5) break;
			}
		}
		// drive distance avg
		$s .= '<tr><td>&nbsp;</td><td align="center" colspan="2"><b>Avg Driver</b></td></tr>';
		arsort($avg_distances);
		$count = 0;
		foreach ($avg_distances as $k => $v) {
			if ($v > 0) {
				$count++;
				$s .= '<tr><td width="1%">'.$count.'</td><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($players_name[$k]).'</a></td><td align="center">'.(sprintf('%.1f',$v)).'</td></tr>';
				if ($count > 5) break;
			}
		}
		// fairway
		$s .= '<tr><td width="1%">&nbsp;</td><td align="center" colspan="2"><b>Fairway Hits</b></td></tr>';
		arsort($fairways);
		$count = 0;
		foreach ($fairways as $k => $v) {
			if ($v > 0) {
				$count++;
				$s .= '<tr><td width="1%">'.$count.'</td><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($players_name[$k]).'</a></td><td align="center">'.(sprintf('%.1f',$v*100)).'%</td></tr>';
				if ($count > 5) break;
			}
		}
		// gir
		$s .= '<tr><td width="1%">&nbsp;</td><td align="center" colspan="2"><b>GIR</b></td></tr>';
		arsort($girs);
		$count = 0;
		foreach ($girs as $k => $v) {
			if ($v > 0) {
				$count++;
				$s .= '<tr><td width="1%">'.$count.'</td><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($players_name[$k]).'</a></td><td align="center">'.(sprintf('%.1f',$v*100)).'%</td></tr>';
				if ($count > 5) break;
			}
		}
		// putts
		$s .= '<tr><td width="1%">&nbsp;</td><td align="center" colspan="2"><b>Putts per hole</b></td></tr>';
		asort($putts);
		$count = 0;
		foreach ($putts as $k => $v) {
			if ($v > 0) {
				$count++;
				$s .= '<tr><td width="1%">'.$count.'</td><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($players_name[$k]).'</a></td><td align="center">'.(sprintf('%.2f',$v)).'</td></tr>';
				if ($count > 5) break;
			}
		}
		// par3
		$s .= '<tr><td width="1%">&nbsp;</td><td align="center" colspan="2"><b>Par 3 avg (2+ rnds)</b></td></tr>';
		asort($par3s);
		$count = 0;
		foreach ($par3s as $k => $v) {
			if ($v > 0) {
				$count++;
				$s .= '<tr><td width="1%">'.$count.'</td><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($players_name[$k]).'</a></td><td align="center">'.(sprintf('%.2f',$v)).'</td></tr>';
				if ($count > 5) break;
			}
		}
		// par4
		$s .= '<tr><td width="1%">&nbsp;</td><td align="center" colspan="2"><b>Par 4 avg (2+ rnds)</b></td></tr>';
		asort($par4s);
		$count = 0;
		foreach ($par4s as $k => $v) {
			if ($v > 0) {
				$count++;
				$s .= '<tr><td width="1%">'.$count.'</td><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($players_name[$k]).'</a></td><td align="center">'.(sprintf('%.2f',$v)).'</td></tr>';
				if ($count > 5) break;
			}
		}
		// par5
		$s .= '<tr><td width="1%">&nbsp;</td><td align="center" colspan="2"><b>Par 5 avg (2+ rnds)</b></td></tr>';
		asort($par5s);
		$count = 0;
		foreach ($par5s as $k => $v) {
			if ($v > 0) {
				$count++;
				$s .= '<tr><td width="1%">'.$count.'</td><td align="center"><a href="/modules.php?name=Golf&op=players_scores&user_id='.$k.'">'.ucwords($players_name[$k]).'</a></td><td align="center">'.(sprintf('%.2f',$v)).'</td></tr>';
				if ($count > 5) break;
			}
		}
	}
	$s .= '<tr><td colspan="3" align="center">Scores must be tracked hole-by-hole for stats calculation</td></tr>';
	$s .= '</table>';
	return $s;
	
}

function tournament_standing_totals( ) {
}

// $tournament_id=0 will show the last tournament completed
function tournament_winners($tournament_id=0) {
	global $db, $user_prefix;	
	
	if ($tournament_id == 0) {
		$today = date('Y-m-d 23:59:59');
		$sql = 'SELECT t.*, UNIX_TIMESTAMP(tournament_date) date FROM '.$user_prefix.'_golf_tournaments t ';
		$sql .= ' WHERE tournament_date <= "'.$today.'" 
		ORDER BY tournament_date DESC LIMIT 1';
		$result = $db->sql_query($sql);
		if ($db->sql_numrows($result)) {
			$row = $db->sql_fetchrow($result);
			$tournament_id = $row['tournament_id'];
			$tournament_name = $row['tournament_name'];
			$tournament_date = date('M-d-Y',$row['date']);
			$tournament_photo_url = $row['tournament_photo_url'];
		}
		//echo $sql;
	}
	$flight_list = array(0=>'');
	$sql = 'select * from '.$user_prefix.'_golf_tournament_flights where tournament_id='.$tournament_id.' order by flight_max_hcp';	
	$result = $db->sql_query($sql);
	if ($db->sql_numrows($result)) {
		while ($row = $db->sql_fetchrow($result)) {
			$flight_list[$row['flight_id']] = ui_form_safe($row['flight_name']);
		}
	}
	
	$first_gross = array();	// 0=a,1=b,etc
	$first_net = array();	// 0=a,1=b,etc
	
	$s = '<table style="border: 1px solid black">
		<tr><th colspan="10">'.$tournament_name.' Results</th></tr>
		<tr><td colspan="10" align="center">'.$tournament_date.'</td></tr>
	<tr>';
	if ($tournament_id) {
		$sql = 'select u.username, u.user_email, gts.*, gr.round_score_temp, gr.round_id, gr2.round_score_temp round_score_temp2, 
			gr2.round_id round_id2, UNIX_TIMESTAMP(signup_time) as signup_time_unix, flight_net_awards, flight_gross_awards,
			player_handicap_temp, gtf.teebox_id, UNIX_TIMESTAMP(player_member_renew_date) player_member_renew_date_unix,
			UNIX_TIMESTAMP(player_member_date) player_member_date_unix from '.
			$user_prefix.'_golf_tournament_signups gts 
			left join '.$user_prefix.'_users u on u.user_id=gts.user_id 
			left join '.$user_prefix.'_golf_rounds gr on (gr.user_id=gts.user_id and gr.tournament_id=gts.tournament_id and gr.user_id > 0)
			left join '.$user_prefix.'_golf_rounds gr2 on (gr2.signup_id=gts.signup_id and gr2.tournament_id=gts.tournament_id and gr2.signup_id > 0)
			left join '.$user_prefix.'_golf_tournament_flights gtf on gtf.flight_id=gts.flight_id
			left join '.$user_prefix.'_golf_players gp on gp.user_id=gts.user_id
			where gts.tournament_id='.$tournament_id.' group by gts.signup_id';
		$result = $db->sql_query($sql);
		if ($db->sql_numrows($result)) {
			while ($row = $db->sql_fetchrow($result)) {
				$name = $row['username']?$row['username']:$row['signup_name'];
				if ($row['round_score_temp']) {
					$round_score = $row['round_score_temp'];
				} else {
					$round_score = $row['round_score_temp2'];
				}
				$standing = '';
				if ($row['signup_gross_standing'] == 1) {
					$standing = '1st flight <b>'.$flight_list[$row['flight_id']].'</b>';
				} else if ($row['signup_net_standing'] == 1) {
					$standing = '1st flight <b>'.$flight_list[$row['flight_id']] .'</b> NET';
					$round_score = $round_score-$row['signup_handicap'] . '&nbsp;NET';
				}
				if (strlen($standing)) {
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
						$s .= '<td>	
						<a href="'.$player_photo_file_full.'" rel="gb_imageset[winner]" title="'.strtoupper($row['username']).' - '.$standing.' - '.$round_score.'">
					  	<img height="50" src="'.$player_photo_file.'" border=0/>
						</a></td>';
					}					
					$s .= '<td valign="top"><b>'.strtoupper($name).'</b><br>'.$standing.'<br><b>'.$round_score.'</b></td>';
				}
			}
		}
		$tourney_photo_file = 'modules/Golf/photos/t'.$tournament_id.'.jpg';
		if (file_exists($tourney_photo_file) || $tournament_photo_url) {
			$s .= '<tr><td colspan="20"><hr></td></tr>';
			$s .= '<tr><td align="center" valign="top" colspan="20">';
			if (file_exists($tourney_photo_file)) {
				$s .= '<a href="'.$tournament_photo_url.'" target="_photo" title="More photos"><img src="'.$tourney_photo_file.'" width="120" border=0/></a>';
			}
			if ($tournament_photo_url) {
				$s .= ' <a href="'.$tournament_photo_url.'" target="_photo" title="More photos"><img src="http://web1.shutterfly.com/img_/SFLY/sfly_lg_ID.gif" border=0/></a> for more photos';
			}
			$s .= '</td></tr>';
		}
	}
	$s .= '</tr></table>';
	
	return $s;
}

?>
