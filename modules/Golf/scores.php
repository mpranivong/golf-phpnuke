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
/* 2005.09.25  Created                                                  */
/************************************************************************/
if (!eregi("modules.php", $_SERVER['PHP_SELF'])) {
	die ("You can't access this file directly...");
}

function scores_show( $recent_rounds = 60, $detail_level = 5, $scroll=true) {
	
	global $db, $user_prefix, $admin, $user, $user_info;
	
	$user_id = get_user_id();
	$player_id = $_GET['user_id'];
	$signup_id = $_GET['signup_id'];
	$course_id = $_GET['course_id'];
	
	$date_format = 'm-d-y';
	$show_count = true;
	$tournament_title = 'Tournament';
	$course_name_length = 20;
	$no_edit = false;
	$show_course = true;
	$show_standing = true;
	$show_tournament = true;
	$show_diff = true;
	$stats_level = 5;
	switch ($detail_level) {
	case 1:
		$show_count = false;
		$date_format = 'Md';
		$show_tournament = false;
		$show_diff = false;
		$show_course = false;
		$tournament_title = 'T';
		$course_name_length = 10;
		$no_edit = true;
		$show_standing = false;
		$stats_level = 1;
	break;
	case 2:
		$show_count = false;
		$date_format = 'Md';
		$tournament_title = 'T';
		$course_name_length = 10;
		$no_edit = true;
		$show_standing = false;
		$stats_level = 1;
	break;
	case 3:
	break;
	case 4:
	break;
	default:
	break;
	}
	
	// show player's other score
	$s = '';
	/*if ($scroll) {
		$s = '<table border="solid 1px" cellpadding="0" cellspacing="0"><tr><td>
		<marquee behavior=\'scroll\' direction=\'up\' height=\'200px\' scrollamount=\'1\' scrolldelay=\'10\' onMouseOver=\'this.stop()\' onMouseOut=\'this.start()\'>';
	}*/
	$s .= '<table border="solid 1px" cellpadding="1" cellspacing="0">';
	
	$sql = 'select gr.round_id, gr.tournament_id, gct.course_id, 
		round_added_by, course_name, gr.user_id, gr.signup_id, teebox_rating, teebox_slope, 
		round_score_temp, date_format(round_date, "%Y%m%d") date_sort, UNIX_TIMESTAMP(round_date) round_date, 
		u.username, gts.signup_name, gct.teebox_name, if(gp.player_handicap_temp is null, 0, player_handicap_temp) player_handicap_temp, 
		gts.signup_net_standing, gts.signup_gross_standing, gr.round_score_diff, round_putts, round_girs, round_fairways,
		round_ryder_points,
		SUM( IF(gh.hole_par-gs.score_value = 0, 1, 0) ) round_pars,
		SUM( IF(gh.hole_par-gs.score_value = 1, 1, 0) ) round_birdies,
		SUM( IF(gh.hole_par-gs.score_value = 2, 1, 0) ) round_eagles,
		SUM(gs.score_drive_distance) total_distance, SUM(IF(gs.score_drive_distance > 0, 1, 0)) distance_count 
		from '.$user_prefix.'_golf_rounds gr 
		left join '.$user_prefix.'_golf_course_teeboxes gct on gct.teebox_id=gr.teebox_id 
		left join '.$user_prefix.'_golf_courses gc on gc.course_id=gct.course_id 
		left join '.$user_prefix.'_golf_scores gs on gs.round_id=gr.round_id
		left join '.$user_prefix.'_golf_holes gh on gh.hole_id=gs.hole_id
		left join '.$user_prefix.'_users u on u.user_id=gr.user_id 
		left join '.$user_prefix.'_golf_tournament_signups gts on (gts.signup_id=gr.signup_id) 
		left join '.$user_prefix.'_golf_players gp on gp.user_id=gr.user_id ';
	//(gts.user_id=gr.user_id and gts.tournament_id=gr.tournament_id) 
		$where = array();
		if ($course_id && $player_id==0 && $signup_id==0) {
			$where[] = 'gc.course_id='.$course_id;
		} else {
			if ($player_id) {
				$where[] = 'gr.user_id='.$player_id;
			} else if ($signup_id) {
				$where[] = 'gr.signup_id='.$signup_id;
			}
		}
		if (count($where)) {
			$sql .= 'where ('.implode(' and ', $where).')';
		}
		$sql .= ' group by gr.round_id order by date_sort desc, round_score_temp';
		if ($recent_rounds) {
			$sql .= ' limit '.$recent_rounds;
		} else {
			$sql .= ' limit 60';
		}
	//echo '<pre>'.$sql.'</pre>';
    $result = $db->sql_query($sql);
	
    if ($db->sql_numrows($result)) {
		if ($player_id) { // calculation player handicap
			// get top 10 of last 20 rounds for handicap
			$last_year = date(strtotime('-365 day'));
			$rounds = 0;
			$tourney_hcp_rounds = 0;
			$tourney_hcp = array();
			$last20_hcp = array();
			while ($row = $db->sql_fetchrow($result)) {
				$round_score = $row['round_score_temp'];
				if ($rounds < 20) {
					$last20_hcp[$row['round_id']] = cal_handicap_diff($round_score, $row['teebox_rating'], $row['teebox_slope']);
					if ($row['tournament_id']) {
						$tourney_hcp_rounds++;
					}
				}
				if ($row['tournament_id']) {
				    // 2010-03-18 gee, get last 20 tournament rounds
				    if (count($tourney_hcp) < 20) {
					//if ($row['round_date'] >= $last_year) {
						$tourney_diff = cal_handicap_diff($round_score, $row['teebox_rating'], $row['teebox_slope']);
						$tourney_hcp[$row['round_id']] = $tourney_diff;
					}
				}
				$rounds++;
			}
			// apply USGA Handicap section 10-2
			asort($last20_hcp);
			$top_half = array();
			$top10_hcp = array();
			$max_round_list = array(5=>1,6=>1,7=>2,8=>2,9=>3,10=>3,11=>4,12=>4,13=>5,14=>5,15=>6,16=>6,17=>7,18=>8,19=>9);
			$max_rounds = ($rounds < 20?$max_round_list[$rounds]:10);
			if (!$max_rounds) $max_rounds = count($last20_hcp);
			$hcp_rounds = 0;
			foreach ($last20_hcp as $k => $v) {
				$hcp_rounds++;
				if ($hcp_rounds <= $max_rounds) {
					$top10_hcp[$k] = $v;
				}
				if ($rounds < 10) {
					if ($hcp_rounds <= ($rounds/2) || $rounds < 2) {
						$top_half[$k] = $v;
					}
				}
			}
			if ($rounds < 10) {	// customer hcp calculation
				if (count($top_half)) {
					$handicap_top_half = (array_sum($top_half)/count($top_half)); //*.96;
					$handicap_top_half = (floor($handicap_top_half*10))/10;
				}
			}
			$handicap = (array_sum($top10_hcp)/count($top10_hcp))*.96;	//sec 10-2.iv
			$handicap = (floor($handicap*10))/10;	// round to tenth w/o rounding up, sec 10-2.v

            // 2010-03-18 gee, hcp using tournament rounds
            $trounds = count($tourney_hcp);
			asort($tourney_hcp);
            $ttop_half = array();
            $ttop10_hcp = array();
            $tmax_round_list = array(5=>1,6=>1,7=>2,8=>2,9=>3,10=>3,11=>4,12=>4,13=>5,14=>5,15=>6,16=>6,17=>7,18=>8,19=>9);
            $tmax_rounds = ($trounds < 20?$tmax_round_list[$trounds]:10);
            if (!$tmax_rounds) $tmax_rounds = count($tourney_hcp);
            $thcp_rounds = 0;
            foreach ($tourney_hcp as $k => $v) {
                $thcp_rounds++;
                if ($thcp_rounds <= $tmax_rounds) {
                    $ttop10_hcp[$k] = $v;
                }
                if ($trounds < 10) {
                    if ($thcp_rounds <= ($trounds/2) || $trounds < 2) {
                        $ttop_half[$k] = $v;
                    }
                }
            }
            if ($trounds < 10) { // customer hcp calculation
                if (count($ttop_half)) {
                    $thandicap_top_half = (array_sum($ttop_half)/count($ttop_half)); //*.96;
                    $thandicap_top_half = (floor($thandicap_top_half*10))/10;
                }
            }
            $thandicap = (array_sum($ttop10_hcp)/count($ttop10_hcp))*.96;  //sec 10-2.iv
            $thandicap = (floor($thandicap*10))/10;   // round to tenth w/o rounding up, sec 10-2.v
			
			// apply USGA Handicap section 10-3
			//2010-03-18 gee, change to tournament rounds only for handicap 
			if (0) { //$tourney_hcp_rounds < 5) { // sec 10-3.iii.c
				if (count($tourney_hcp) > 1) {
					asort($tourney_hcp);
					$tourney_low_rounds = 0;
					$tourney_2nd_low_diff = 0;
					$tourney_1st_low_diff = 0;
					$tourney_hcp_rounds = 0;
					foreach ($tourney_hcp as $k => $v) {
						if ( ($handicap - $v) > 3) { // sec. 10-3
							$tourney_low_rounds++;
							if ($tourney_low_rounds == 1) {	// sec. 10-3 (iii)
								$tourney_1st_low_diff = $v;
							}
							if ($tourney_low_rounds == 2) {	// sec. 10-3 (ii)
								$tourney_2nd_low_diff = $v;
							}
						}
						if (($handicap - $v) > 1) {
							$tourney_hcp_rounds++;
						}
					}
					if ($tourney_low_rounds > 1) {
						if (($handicap - $tourney_2nd_low_diff) > 3) {	// sec. 10-3 (ii)
							$tourney_low_diff_avg = ($tourney_1st_low_diff+$tourney_2nd_low_diff)/2; // 10-3 b iii
							$handicap_tourney_diff = $handicap - $tourney_low_diff_avg; // 10-3 b iv
							
							$reduction_table = array(4.0 => array(2 => 1));
							
							$tourney_handicap = $tourney_low_diff_avg + $tourney_diff_adjustment;
							$tournament_method = true;
						}
					}
				}
			}
			
			
			$handicap2 = $handicap>36.4?36.4:($rounds<10?$handicap_top_half:$handicap);
			$thandicap2 = $thandicap>36.4?36.4:($trounds<10?$thandicap_top_half:$thandicap); 
			//$s .= '<tr><th colspan="9 align="center"><b>'.sprintf('%.1f', ($handicap>36.4?36.4:($rounds<10?$handicap_top_half:$handicap))).'</b>'.($rounds < 5?'&nbsp;<a href="javascript:alert(\'minimum 5 rounds are require for valid USGA handicap.\');">(not a valid handicap)</a>':'').'</th></tr>';
			$s .= '<tr><th colspan="9 align="center"><b>'.sprintf('%.1f(t)/%.1f', $thandicap2, $handicap).'</b>'.($trounds < 5?'&nbsp;<a href="javascript:alert(\'minimum 5 rounds are require for valid USGA handicap.\');">(not a valid handicap)</a>':'').'</th></tr>';
			if (thandicap_top_half) {
				$s .= '<tr><td colspan="9" align="center"><b>'.sprintf('%.1f', $handicap_top_half).'</b>(special calculation, best top half rounds for less than 10 rounds played)</td></tr>';
			}
			if ($tournament_method) {
				$s .= '<tr><th colspan="9" align="center"><span style="font-weight:bold;">'.sprintf('%.1f', $tourney_handicap).'</span> (T)</th></tr>';
			}
			$s .= '<tr><td colspan="9" align="center">(* = 10 rounds with lowest handicap differentials, USGA Handicap sec. 10-1)</td></tr>'; 
			if ($handicap > 36.4) {
				$s .= '<tr><td colspan="9" align="center">(* = USGA capped handicap at 36.4)</td></tr>';
			}
			if ($rounds < 20) {
				$s .= '<tr><td colspan="9" align="center">(* = 20 rounds requires for full handicap calculation, USGA Handicap sec. 10-2)</td></tr>';
			}
			if ($tournament_method) {
				// do not use this method if more than 5 tournament rounds are used in the top 10 rounds
				$s .= '<tr><td colspan="9" align="center">(T = exceptional tournament rounds used in handicap calculation, USGA Handicap section 10-3)</td></tr>';
			}
			if ($rounds < 5) {
				$top10_hcp = array();	// do not consider 5 rounds or less
			}
			// update temp calculation so other area of website do not have to calculate handicap
			//$sql = 'UPDATE '.$user_prefix.'_golf_players SET player_handicap_temp = '.($handicap>36.4?36.4:($rounds<10?$handicap_top_half:$handicap)).' WHERE user_id='.$player_id.' LIMIT 1';
			$sql = 'UPDATE '.$user_prefix.'_golf_players SET player_handicap_temp = '.($thandicap2).' WHERE user_id='.$player_id.' LIMIT 1';
			$hresult = $db->sql_query($sql);
		}
		
		$s .= '<tr>'.($show_count?'<td>&nbsp;</td>':'').'<td style="font-weight:bold;">Date</td>';
		if ($player_id) {
			if ($show_course) {
				$s .= '<td style="font-weight:bold;">Course</td>';
				$s .= '<td style="font-weight:bold;">Tee Box</td>';
			}
		} else {
			$s .= '<td style="font-weight:bold;" colspan="2">Player</td>';
			if ($show_course) {
				$s .= '<td style="font-weight:bold;">'.($course_id?'Teebox':'Course').'</td>';
			}
		}
		$s .= '<td style="font-weight:bold;">Score</td>';
		if ($show_diff) {
			$s .= '<td style="font-weight:bold;">Diff</td>';
		}
		$s .= '<td>Highlights</td>';
		if ($show_tournament) {
			$s .= '<td style="font-weight:bold;">'.$tournament_title.'</td>';
		}
		if ($player_id) {
			$s .= '<td style="font-weight:bold;"><a title="R=Ryder Cup, C=Champion Cup">Points</a></td>';
		}
		if (!$no_edit) {
			$s .= '<td>&nbsp;</td>'.($admin?'<td>&nbsp;</td>':'');
		}
		$s .= '</tr>';
		
		// display all rounds
		$rounds = 1;
		$db->sql_rowseek(0,$result);
		while ($row = $db->sql_fetchrow($result)) {
			$round_score = $row['round_score_temp'];
			$course_title = 'title="'.ui_form_safe($row['course_name']).'"';
			$s .= '
			<tr>'.
				($show_count?'<td>'.$rounds++.'</td>':'').'
				<td>'.date_relative($date_format, $row['round_date']).'</td>';
				if ($player_id) {
					$s .= '<td nowrap><a href="/modules.php?name=Golf&op=courses&course_id='.$row['course_id'].'" '.$course_title.'>'.ui_form_safe(substr($row['course_name'], 0, $course_name_length)).'</a></td>';
					$s .= '<td><a title="'.ui_form_safe($row['teebox_name']).'">'.ui_form_safe(substr($row['teebox_name'], 0, 10)).'</a></td>';
				} else {
					if ($row['username']) {

						$s .= '<td>';
						
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
							$s .= '	
							<a href="'.$player_photo_file_full.'" rel="gb_imageset[recent]" title="'.strtoupper($row['username']).' - '.$round_score.' - '.$row['course_name'].'">
						   	<img height="20" src="'.$player_photo_file.'" border=0/>
							</a';
						}

						
						$s .= '</td>';
						$s .= '<td><a href="/modules.php?name=Golf&op=players_scores&user_id='.$row['user_id'].'" '.$course_title.'>'.ui_form_safe($row['username']).'</a></td>';
					} else {
						$s .= '<td></td><td><a href="/modules.php?name=Golf&op=players_scores&signup_id='.$row['signup_id'].'" '.$course_title.'>'.ui_form_safe($row['signup_name']).'</a></td>';
					}
					if ($course_id) {
						$s .= '<td><a title="'.ui_form_safe($row['teebox_name']).'">'.ui_form_safe(substr($row['teebox_name'], 0, 10)).'</a></td>';
					} else {
						if ($show_course) {
							$s .= '<td nowrap><a href="/modules.php?name=Golf&op=courses&course_id='.$row['course_id'].'" '.$course_title.'>'.ui_form_safe(substr($row['course_name'], 0, $course_name_length)).'...</a>'.'</td>';
						}
					}
				}
				
				$diff = cal_handicap_diff($round_score, $row['teebox_rating'], $row['teebox_slope']);
				
				$bold = false;
				$tourney = false;
				if (isset($top10_hcp[$row['round_id']])) {
					//$bold = true;
				}
				if (1) { //$tournament_method) {
					if (isset($tourney_hcp[$row['round_id']])) {
						$tourney = true;
						$bold = true;
					}
				}
				$s .= '<td style="text-align:center; white-space:nowrap;">'.($diff-$row['player_handicap_temp'] < 3?'<b>':'').'<a href="/modules.php?name=Golf&op=players_scores&round_id='.$row['round_id'].'&user_id='.$row['user_id'].'">'.$round_score.'</a>'.($diff-$row['player_handicap_temp'] < 3?'</b><img src="/modules/Web_Links/images/popular.gif"/>':'').'</td>';
				if ($row['tournament_id']) {
					$tournament='<a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$row['tournament_id'].'">';
					if ($show_standing) {
						$tourney_result = '';
						if ($row['signup_gross_standing']>0 && $row['signup_gross_standing']<20) {
							$tourney_result .= '(G)'.date('jS', mktime(0, 0, 0, 1, $row['signup_gross_standing'], 2005));
						}
						if ($row['signup_net_standing']>0 && $row['signup_net_standing']<20) {
							$tourney_result .= ' (N)'.date('jS', mktime(0, 0, 0, 1, $row['signup_net_standing'], 2005));
						}
						if (strlen($tourney_result) == 0) $tourney_result = 'X';
						$tournament .= $tourney_result;
					} else {
						$tournament .= 'X';
					}
					$tournament .= '</a>';
				} else {
					$tournament = '&nbsp;';
				}
				if ($show_diff) {
					$s .= '<td>'.($bold?'<b>':'').sprintf('%.1f',$diff).($bold?'*'.($tourney?'T':'').'</b>':'').'</td>';
				}
				$s .= '
				<td nowrap style="font-size:9;"><b>';
				$highlights = array();
				if ($row['round_eagles']) {
					$highlights[] = $row['round_eagles']. ' <b>eagles</b>';
				}
				if ($row['round_birdies']) {
					$highlights[]  = $row['round_birdies']. ' <b>birdies</b>';
				}
				if ($row['round_pars'] > 5) {
					$highlights[]  = $row['round_pars']. ' pars';
				}
				if ($row['round_fairways']>7) {
					$highlights[] = sprintf('%.0f', $row['round_fairways']/14*100) . '%fairways';
				}
				if ($row['round_girs']>9) {
					$highlights[] = sprintf('%.0f', $row['round_girs']/18*100) . '%GIR';
				}
				if ($row['round_putts']<30 && $row['round_putts'] > 18) {
					$highlights[] = $row['round_putts'] . ' putts';
				}
				if ($row['distance_count'] >= 9) {
					$avg_distance = $row['total_distance']/$row['distance_count'];
					if ($avg_distance > 250) {
						$highlights[] = sprintf('%d', $avg_distance) . 'y avg';
					}
				}
				cal_points( 0, $row['round_date'], $row['tournament_id'], $row['round_ryder_points'], $row['signup_gross_standing'], $row['signup_net_standing'], $ryder_points, $champion_points );								
				$s .= implode('<br>', $highlights).'&nbsp;</b></td>';
				if ($show_tournament) {
					$s .= '<td align="center">'.(strlen($tournament)?$tournament:'&nbsp;').'</td>';
				}
				
				if ($player_id) {
					$rc_points=0; $ch_points=0;
					foreach (@$ryder_points as $k => $v) { $rc_points += $v; }
					foreach (@$champion_points as $k => $v) { $ch_points += $v; }
					if ($row['tournament_id']) {
						$s .= '<td>R'.sprintf('%.0f', $rc_points).'&nbsp;C'.sprintf('%.0f', $ch_points).'</td>';
					} else {
						$s .= '<td>&nbsp;</td>';
					}
				}
				$s .= '<td>';
				// update diff
				if (round($diff, 1) != $row['round_score_diff']) {
					$db->sql_query('update '.$user_prefix.'_golf_rounds set round_score_diff='.$diff.' where round_id='.$row['round_id'].' limit 1');
				}
			if (!$no_edit) {
				if ($admin || ( (($user_id == $row['user_id']) || ($user_id == $row['round_added_by'])) && $user_id ) ) {
					$s .= '<a href="/modules.php?name=Golf&op=players_scores&user_id='.$row['user_id'].'&round_id='.$row['round_id'].'">edit</a>';
				}
				$s .= '&nbsp;</td>';
				if ($admin) {
					$s .= '<td><a href="/modules.php?name=Golf&op=players_scores_delete&user_id='.$row['user_id'].'&round_id='.$row['round_id'].'">del</a></td>';
				}
			}
			$s .= '</tr>';
		}
		$s .= '<tr><td colspan="10" align="center"><img src="/modules/Web_Links/images/popular.gif"/> = personal exceptional round'.($detail_level > 3?' (diff is within 3 or less from handicap)':'').'</td></tr>';
	}
	$s .= '</table>';
	/*if ($scroll) {
		$s .= '</marquee></td></tr></table>';
	}*/
	return $s;
	
}

?>
