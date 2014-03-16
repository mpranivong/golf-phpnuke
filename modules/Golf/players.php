<?php
/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2005 by M. Pranivong (tourney@dfwlga.com)              */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* GOLF MODULE                                                          */
/* 2005.09.14  Created                                                  */
/************************************************************************/
if (!eregi("modules.php", $_SERVER['PHP_SELF'])) {
	die ("You can't access this file directly...");
}
include_once('./modules/Golf/scores.php');

function players($detail_level=5) {
	global $db, $user_prefix, $admin, $user, $user_info;
	global $member_titles;

	$show_title = true; $show_play = true; $show_member_date = true; $show_enter_score = true;
	switch ($detail_level) {
	case 1:
		$show_title = false; $show_play = false; $show_member_date = false; $show_enter_score = false;
	break;
	default:
	break;
	}

	$user_id = $_GET['user_id'];

	$s = '<table align="center" border=1>';
	$s .= '<tr><td><table align="center"><tr><td><b>Players/Stats</b></td></tr></table></td></tr>';
	$s .= '<tr><td><table align="center"><tr><td>(register <a href="/modules.php?name=Your_Account&op=new_user">here</a> to track your stats)</td></tr></table></td></tr>';

	$s .= '<tr><td><table border="1px solid black" cellpadding=2 cellspacing=0 align="center">';

	$s .= '<tr><td></td><td colspan="2"><b>Users</b></td>'.($show_play?'<td><b>Plays Golf</b></td>':'').($show_member_date?'<td><b>Member Since</b></td>':'').'<td><b>HCP</b></td><td><b>Tournament Results</b></td>
		<td><b>Stats</b></td>';
	if ($show_enter_score) {
		$s .= '<td>enter scores<br>(<a href="/modules.php?name=Your_Account">login</a> to enter score)</td>';
	}
	$s .= '</tr>';
	$edit_row=array();
	$sql = 'select gp.*, u.username, u.user_id, UNIX_TIMESTAMP(player_member_date) player_member_date_unix,
		UNIX_TIMESTAMP(player_member_renew_date) player_member_renew_date_unix,
		sum(if(gs.signup_net_standing=1,1,0)) first_place_net,
		sum(if(gs.signup_gross_standing=1,1,0)) first_place_gross,
		sum(if(gs.signup_net_standing=2,1,0)) second_place_net,
		sum(if(gs.signup_gross_standing=2,1,0)) second_place_gross,
		sum(if(gs.signup_net_standing=3,1,0)) third_place_net,
		sum(if(gs.signup_gross_standing=3,1,0)) third_place_gross
		from '.
		$user_prefix.'_users u left join '.$user_prefix.'_golf_players gp on gp.user_id=u.user_id
		left join '.$user_prefix.'_golf_tournament_signups gs on gs.user_id=gp.user_id
		group by u.user_id
		order by player_member desc, player_handicap_temp asc, username asc';

	//echo '<pre>'.$sql.'</pre>';
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		while ($row = $db->sql_fetchrow($result)) {
			$s .= '<tr>';
			if ($show_title)  {
				$s .='<td>'.
				($row['player_member_title']?$member_titles[$row['player_member_title']]:'').'</td>';
			}

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
				$s .= '<td>
				<a href="'.$player_photo_file_full.'" rel="gb_imageset[winner]" title="'.strtoupper($row['username']).' - '.$standing.' - '.$round_score.'">
			  	<img height="50" src="'.$player_photo_file.'" border=0/>
				</a</td>';
			}

			$s .= '</td>';

			$s .= '
			<td>'.($admin?'<a href="/modules.php?name=Golf&op=players&user_id='.$row['user_id'].'">':'').ui_form_safe($row['username']).($admin?'</a>':'').'</td>';
			if ($show_play) {
				$s .= '<td>'.($row['player_id']?'Yes':'?').'</td>';
			}
			if ($show_member_date) {
				$s .= '
				<td>';
				if ($row['player_member']) {
					$player_member_date = 'Yes';
					if (intval($row['player_member_date_unix'])) {
						$player_member_date = date('m-d-y', $row['player_member_date_unix']);
					}
				} else {
					$player_member_date = '?';
				}
				$s .= $player_member_date . '</td>';
			}
			$s .= '
			<td><a href="/modules.php?name=Golf&op=players_scores&user_id='.$row['user_id'].'">'.sprintf('%.1f',$row['player_handicap_temp']).'</a></td>
			<td>';
			if ($row['first_place_gross'] || $row['first_place_net']) {
				$s .= '  <i>1st:</i> '.($row['first_place_net']+$row['first_place_gross']);
			}
			if ($row['second_place_gross'] || $row['second_place_net']) {
				$s .= '  <i>2nd:</i> '.($row['second_place_net']+$row['second_place_gross']);
			}
			if ($row['third_place_gross'] || $row['third_place_net']) {
				$s .= '  <i>3rd:</i> '.($row['third_place_net']+$row['third_place_gross']);
			}
			$s .= '&nbsp;
			</td>
			<td><a href="/modules.php?name=Golf&op=players_stats&user_id='.$row['user_id'].'">stats</a></td>';
			if ($show_enter_score) {
				if ($admin || $user_info['user_id'] == $row['user_id']) {
					$s .= '<td><a href="/modules.php?name=Golf&op=players_scores&user_id='.$row['user_id'].'#players_addedit">enter score</a></td>';
				} else {
					$s .= '<td>&nbsp;</td>';
				}
			}
			$s .= '</tr>';
			if ($user_id==$row['user_id']) {
				$edit_row = $row;
			}
		}
	}
	$s .= '</table></td></tr>';
	if ($user_id) {
		$s .= '<tr><td><a name="players_addedit"/><table align="center"><tr><td><b>'.($edit_row?'Edit':'Add').' Player</b></td></tr></table></td></tr>';
		$s .= '<form action="/modules.php?name=Golf&op=players_addedit" method="POST"/>';
		$s .= '<tr><td><table border="1px solid black" cellpadding=2 cellspacing=0 align="center">';
		$readonly = true;
		if ($admin) {
			$readonly = '';
		}
		$player_member_date='';
		if (intval($edit_row['player_member_date_unix'])) {
			$player_member_date = date('Y-m-d', $edit_row['player_member_date_unix']);
		}
		if (intval($edit_row['player_member_renew_date_unix'])) {
			$player_member_renew_date = date('Y-m-d', $edit_row['player_member_renew_date_unix']);
		}

		$s .= '<input type="hidden" name="player_id" value="'.$edit_row['player_id'].'"/>';
		$s .= '<input type="hidden" name="user_id" value="'.$user_id.'"/>';
		$s .= '<input type="hidden" name="player_handicap_temp" value="'.$edit_row['player_handicap_temp'].'"/>';
		$s .= '<tr><td align="right">Player ID:</td><td>'.$edit_row['username'].'</td></tr>';
		$s .= '<tr><td align="right">Member</td><td><input type="checkbox" name="player_member" value="1" '.($edit_row['player_member']?'checked':'').' '.$readonly.'/></td></tr>';
		$s .= '<tr><td align="right">Member Since:</td><td><input type="text" name="player_member_date" value="'.$player_member_date.'" size="20" maxlength="20" '.$readonly.'/> yyyy-mm-dd</td></tr>';
		$s .= '<tr><td align="right">Renewal:</td><td><input type="text" name="player_member_renew_date" value="'.$player_member_renew_date.'" size="20" maxlength="20" '.$readonly.'/> yyyy-mm-dd</td></tr>';
		$s .= '<tr><td align="right">Membership Length:</td><td><input type="text" name="player_member_length" value="'.$edit_row['player_member_length'].'" size="4" maxlength="2" '.$readonly.'/> (in years)</td></tr>';
		$s .= '<tr><td align="right">Member Title:</td><td>'.ui_select_box($member_titles, 'player_member_title', $edit_row['player_member_title'], ($readonly?'disabled':'')).'</td></tr>';
		$s .= '<tr><td align="right">USGA GHIN:</td><td><input type="text" name="player_ghin" value="'.$edit_row['player_ghin'].'" size="20" maxlength="20" '.$readonly.'/></td></tr>';
		$s .= '<tr><td align="right">Starting Handicap:</td><td><input type="text" name="player_handicap_start" value="'.$edit_row['player_handicap_start'].'" size="4" maxlength="4" '.$readonly.'/> (to help with HCP calculation until player has 20 rounds)</td></tr>';
		$s .= '<tr><td align="right">Temp Handicap:</td><td><input type="text" name="player_handicap_temp" value="'.$edit_row['player_handicap_temp'].'" size="4" maxlength="4" '.$readonly.'/> (manually adjusted handicap)</td></tr>';
		$s .= '<tr><td align="right">Current HCP:</td><td>'.$edit_row['player_handicap_temp'].' (calculation from recorded rounds)</td></tr>';
		$s .= '<tr><td align="right">Note:</td><td><textarea name="player_note" cols="60" rows="3" '.$readonly.'>'.ui_form_safe($edit_row['player_note']).'</textarea><br>(1000 chars max)</td></tr>';
		if ($admin) {
			$s .= '<tr><td>&nbsp;</td><td><input type="submit" value="update"/></td></tr>';
		}
		$s .= '</table></td></tr>
			</form>';
	}
	$s .= '</table>';
	echo $s;
}
function players_addedit() {
	global $user, $admin, $db, $user_prefix, $user_info;
	$player_id = $_POST['player_id'];
	if(!$admin) {
		echo '<span class="error">Admin only.</span>';
		return;
	}

	if ($player_id) {
		if (intval($_POST['player_handicap_temp']) == 0) {
			$_POST['player_handicap_temp'] = $_POST['player_handicap_start'];
		}
		$sql = 'UPDATE '.$user_prefix.'_golf_players SET
			player_member="'.FixQuotes($_POST['player_member']).'",
			player_member_date="'.FixQuotes($_POST['player_member_date']).'",
			player_member_renew_date="'.FixQuotes($_POST['player_member_renew_date']).'",
			player_member_length="'.FixQuotes($_POST['player_member_length']).'",
			player_member_title="'.FixQuotes($_POST['player_member_title']).'",
			player_ghin="'.FixQuotes($_POST['player_ghin']).'",
			player_handicap_start="'.FixQuotes($_POST['player_handicap_start']).'",
			player_handicap_temp="'.FixQuotes($_POST['player_handicap_temp']).'",
			player_note="'.FixQuotes($_POST['player_note']).'"
			WHERE player_id='. $player_id;
	} else {
		$course_added_by = get_user_id();
		$sql = 'INSERT INTO '.$user_prefix.'_golf_players (player_member, player_member_date, player_member_renew_date,
			player_member_length, player_member_title, player_ghin, player_handicap_start, player_handicap_temp,
			player_note, user_id)
			VALUES ("'.FixQuotes($_POST['player_member']).'","'.FixQuotes($_POST['player_member_date']).'",
			"'.FixQuotes($_POST['player_member_renew_date']).'",
			"'.FixQuotes($_POST['player_member_length']).'","'.FixQuotes($_POST['player_member_title']).'",
			"'.FixQuotes($_POST['player_ghin']).'",
			"'.FixQuotes($_POST['player_handicap_start']).'","'.FixQuotes($_POST['player_handicap_start']).'",
			"'.FixQuotes($_POST['player_note']).'",
			"'.FixQuotes($_POST['user_id']).'")';
	}

	$result = $db->sql_query($sql);
	//echo '<pre>'.$sql.'</pre>';
    if(!$result) {
    	echo ""._ERROR."<br>$sql<br>";
		exit();
    }
	header('Location: /modules.php?name=Golf&op=players&user_id='.$_POST['user_id']);

}
function players_scores() {
	global $db, $user_prefix, $admin, $user, $user_info;

	$start_over = $_GET['start_over'];
	$round_id = $_GET['round_id'];
	$round_added_by = get_user_id();
	$round_date = $_POST['round_date']?$_POST['round_date']:date('Y-m-d');
	$player_id = isset($_POST['user_id'])?$_POST['user_id']:$_GET['user_id'];
	if (!$round_id) {
		$signup_id = $_GET['signup_id'];
	}
	$tournament_id = isset($_POST['tournament_id'])?$_POST['tournament_id']:$_GET['tournament_id'];
	$course_id = isset($_POST['course_id'])?$_POST['course_id']:$_GET['course_id'];
	$teebox_id = isset($_POST['teebox_id'])?$_POST['teebox_id']:$_GET['teebox_id'];

	$signup_name = "";
	if ($player_id == 0) {
		if ($signup_id > 0) {
			$result = $db->sql_query('select signup_name from '.$user_prefix.'_golf_tournament_signups where signup_id='.$signup_id);
			if ($db->sql_numrows($result)) {
				$row = $db->sql_fetchrow($result);
				$signup_name = $row['signup_name'];
			}
		}
	}

	// get tournament list
	$tournament_list = array(0=>'');
	$sql = 'select gt.tournament_id, tournament_name, teebox_name, gc.course_name, gc2.course_name course_name2, gt.course_id, gt.teebox_id, UNIX_TIMESTAMP(tournament_date) tournament_date
		from '.$user_prefix.'_golf_tournaments gt
		left join '.$user_prefix.'_golf_course_teeboxes gct on gct.teebox_id='.($teebox_id?$teebox_id:'gt.teebox_id'). '
		left join '.$user_prefix.'_golf_courses gc on gc.course_id=gt.course_id
		left join '.$user_prefix.'_golf_courses gc2 on gc2.course_id=gct.course_id
		order by tournament_name';
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		while ($row = $db->sql_fetchrow($result)) {
			$tournament_list[$row['tournament_id']] = $row['tournament_name'];
			if ($tournament_id == $row['tournament_id']) {
				$tournament_name = $row['tournament_name'];
				if (!$start_over) {
					if (!$teebox_id) $teebox_id = $row['teebox_id'];
					$teebox_name = $row['teebox_name'];
				}
				$course_id = $row['course_id'];
				$course_name = $row['course_name']?$row['course_name']:$row['course_name2'];
				$score_date = $row['tournament_date'];
				$round_date = date('Y-m-d',$row['tournament_date']);
			}
		}
	}
//echo $teebox_id. ' '. $course_id . ' ' . $course_name . ' ' . $score_date . '  ' . $round_date . ' ' . $teebox_name;
	// get player list
	$player_list = array();
	$sql = 'select user_id, username from '.$user_prefix.'_users order by username';
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		while ($row = $db->sql_fetchrow($result)) {
			$player_list[$row['user_id']] = ui_form_safe($row['username']);
		}
	}

	// get course list
	$course_list = array(0=>'');
    $result = $db->sql_query('select course_id, course_name from '.$user_prefix.'_golf_courses order by course_name');
    if ($db->sql_numrows($result)) {
		while ($row = $db->sql_fetchrow($result)) {
			$course_list[$row['course_id']] = ui_form_safe($row['course_name']);
			if ($course_id == $row['course_id']) $course_name = $row['course_name'];
		}
	}

	if ($round_id > 0) {
		$sql = 'select gr.*, course_name, gc.course_id, tournament_name, teebox_name, UNIX_TIMESTAMP(round_date) round_date,
			gts_u.signup_name, gts_u.signup_gross_standing, gts_u.signup_net_standing
			from '.$user_prefix.'_golf_rounds gr
			left join '.$user_prefix.'_golf_course_teeboxes gct on gct.teebox_id=gr.teebox_id
			left join '.$user_prefix.'_golf_courses gc on gc.course_id=gct.course_id
			left join '.$user_prefix.'_golf_tournaments gt on gt.tournament_id=gr.tournament_id
			left join '.$user_prefix.'_golf_tournament_signups gts_u on (gts_u.signup_id=gr.signup_id)
			where round_id='.$round_id;
		//left join '.$user_prefix.'_golf_tournament_signups gts_u on (gts_u.user_id=gr.user_id and gts_u.tournament_id=gr.tournament_id)
		//left join '.$user_prefix.'_golf_tournament_signups gts on gts.signup_id=gr.signup_id
		//echo '<pre>'.$sql.'</pre>';
		$result = $db->sql_query($sql);
		if ($db->sql_numrows($result)) {
			$round_row = $db->sql_fetchrow($result);
			if ($round_row['user_id']) $player_id = $round_row['user_id'];
			$signup_id = $round_row['signup_id'];
			if (!$start_over) {
				$teebox_id = $round_row['teebox_id'];
				$teebox_name = $round_row['teebox_name'];
			}
			$tournament_id = $round_row['tournament_id'];
			$tournament_name = $round_row['tournament_name'];
			if ($tournament_id) {
				$course_name = $round_row['course_name'];
				$course_id = $round_row['course_id'];
			}
			$round_added_by = $round_row['round_added_by'];
			$round_date = date('Y-m-d',$round_row['round_date']);
			$round_score = $round_row['round_score'];
			$signup_name = $round_row['signup_name'];
		}
	}

	// get teebox list
	if ($course_id) {
		$teebox_list = array(0=>'');
		$result = $db->sql_query('select teebox_id, teebox_name, teebox_rating, teebox_slope from '.$user_prefix.'_golf_course_teeboxes
			where course_id='.$course_id.' order by teebox_slope desc');
		if ($db->sql_numrows($result)) {
			while ($row = $db->sql_fetchrow($result)) {
				$teebox_list[$row['teebox_id']] = ui_form_safe($row['teebox_name']. ' ('.sprintf('%.1f',$row['teebox_rating']).'/'.$row['teebox_slope'].')');
				if ($teebox_id == $row['teebox_id']) $teebox_name = $row['teebox_name'];
			}
		}
	}

	if ($teebox_id > 0) {
		$cresult = $db->sql_query('select course_id from '.$user_prefix.'_golf_course_teeboxes where teebox_id='.$teebox_id);
		if ($db->sql_numrows($cresult)) {
			$crow = $db->sql_fetchrow($cresult);
			$course_id = $crow['course_id'];
		}
        }
	if ($start_over) {
		if (!isset($_POST['tournament_id'])) {
			$tournament_id = NULL;
			$tournament_name = NULL;
		}
		if (!isset($_POST['teebox_id'])) {
			$teebox_id = NULL;
			$teebox_name = NULL;
		}
		if (!isset($_POST['course_id'])) {
			$course_id = NULL;
			$course_name = NULL;
		}
	}

	$s = '<table align="center">';

	if (is_user($user) || $admin) {
		$form_action = "/modules.php?name=Golf&op=players_scores";
		$s .= '<input type="hidden" name="round_added_by" value="'.$round_added_by.'"/>';
		$s .= '<input type="hidden" name="round_id" value="'.$round_id.'"/>';
		$s .= '<input type="hidden" name="tournament_id" value="'.$tournament_id.'"/>';
		$s .= '<input type="hidden" name="teebox_id" value="'.$teebox_id.'"/>';
		$s .= '<input type="hidden" name="course_id" value="'.$course_id.'"/>';

		$s .= '<table align="center">';
		$s .= '<tr><td><table align="center"><tr><td><b>'.($round_id?'Edit':'Add').' Score</b></td></tr></table></td></tr>';
		$s .= '<tr><td>
			<table align="center" border="solid 1px" cellpadding="0" cellspacing="0" width="1%">
				<tr><td align="right">Player:</td><td>';

			if ($player_id > 0 || (strlen($signup_name)==0 || $signup_id==0)) {
				$s .= ui_select_box($player_list, 'user_id', $player_id>0?$player_id:get_user_id());
			} else {
				$s .= ui_form_safe($signup_name).'<input type="hidden" name="signup_id" value="'.$signup_id.'"/>';
			}
			$s .= '</td><td>&nbsp;</td></tr>';
			$s .= '<tr><td align="right">Date:</td><td nowrap><input type="text" name="round_date" value="'.$round_date.'" size="16" maxlength="20"'.($tournament_id?' readonly':'').'/>&nbsp;yyyy-mm-dd</td><td>&nbsp;</td></tr>';
			if (isset($tournament_name)) {
				$s .= '<tr><td align="right">Tournament:</td><td nowrap>'.ui_form_safe($tournament_name).', standing: G'.$round_row['signup_gross_standing']. ' N'.$round_row['signup_net_standing'].'</td><td>&nbsp;</td></tr>';
			}
			if (isset($course_name) || strlen($round_row['course_name'])) {
				$s .= '<tr><td align="right">Course:</td><td nowrap>'.ui_form_safe(isset($course_name)?$course_name:$round_row['course_name']).'</td><td>&nbsp;</td></tr>';
			}
			if (isset($teebox_name)) {
				$s .= '<tr><td align="right">Teebox:</td><td nowrap>'.ui_form_safe($teebox_name).'&nbsp;<a href="/modules.php?name=Golf&op=players_scores&round_id='.$round_id.'&user_id='.$player_id.'&start_over=1">change</a></td><td>&nbsp;</td></tr>';
			}
			if (strlen($tournament_id) == 0 && $course_id == 0) {
				$s .= '<tr><td align="right">Tournament:</td><td nowrap>'.ui_select_box($tournament_list, 'tournament_id', $tournament_id).
					'&nbsp;<input type="submit" value="select"/></td><td>(optional)</td></tr>';
				$s .= '<tr><td align="right">Course:</td><td nowrap>'.ui_select_box($course_list, 'course_id', $course_id).
					'&nbsp;<input type="submit" value="select"/></a></td><td nowrap>(if the course is not listed, please add it <a href="/modules.php?name=Golf&op=courses">here</a>)</td></tr>';
			} elseif ($teebox_id == 0) {
				$s .= '<tr><td align="right">Teebox:</td><td nowrap>'.ui_select_box($teebox_list, 'teebox_id', $teebox_id).
					'&nbsp;<input type="submit" value="select"/></a></td><td nowrap>(if the teebox is not listed, please add it <a href="/modules.php?name=Golf&op=teeboxes&course_id='.$course_id.'">here</a>)</td></tr>';

			} else {

				$start_over = 0;
				$form_action ="/modules.php?name=Golf&op=players_scores_addedit";

				$s .= '<tr><td align="right"><b>Total Score</td><td><input type="text" name="round_score" value="'.($round_score>0?$round_score:'').'" size="4" maxlength="3"/>&nbsp;leave this blank if you are entering score hole-by-hole below</td></tr>';

				// get scores
				$scores = array();
				if ($round_id) {
					$result = $db->sql_query('select * from '.$user_prefix.'_golf_scores gs
						left join '.$user_prefix.'_golf_holes gh on gh.hole_id=gs.hole_id
						where round_id='.$round_id.' order by hole_number asc');
					if ($db->sql_numrows($result)) {
						while ($row = $db->sql_fetchrow($result)) {
							$scores[$row['hole_number']] = $row;
						}
					}
				}
				$hresult = $db->sql_query('select hole_id, hole_number, hole_par, hole_handicap, hole_distance from '.$user_prefix.'_golf_holes where teebox_id='.$teebox_id.' order by hole_number asc');
				if ($db->sql_numrows($hresult)) {
					// display holes/scores
					$s .= '<tr><td align="center" colspan="2"><b>Or</td></tr>';
					$s .= '<tr><td align="center" colspan="2"><b>Hole-by-Hole</td></tr>';
					$s .= '<tr><td colspan="2">
						<table border="solid 1px" cellpadding="1" cellspacing="0">';
					$hrow = '<tr><td align="right">Hole:</td>';	// hole #
					$parrow = '<tr><td align="right">Par</td>';	// par
					$srow = '<tr><td align="right">Score:</td>';	// score
					$drow = '<tr><td align="right" nowrap>Driver Distance:</td>';	// distance
					$prow = '<tr><td align="right">Putts:</td>';	// putts
					$frow = '<tr><td align="right" nowrap>Fairway Hit:</td>';	// fairway
					$grow = '<tr><td align="right" nowrap>Green in Regulation:</td>'; // green
					$havgrow = '<tr><td align="right" nowrap>Hole Avg:</td>'; // hole avg
					$rcrow = '<tr><td align="right" nowrap>Ryder Cup Point:</td>'; // ryder cup points
					$chrow = '<tr><td align="right" nowrap>Champion Cup Point:</td>'; // champion cup points
					$hcprow = '<tr><td align="right">Handicap</td>';	// handicap
					$in = 0; $out = 0; $s_in = 0; $s_out = 0; $fairway = 0; $green = 0; $drive_avg = 0; $drive_avg_holes = 0; $putts = 0; $rc_points=0;
					while ($row = $db->sql_fetchrow($hresult)) {
						$h = $row['hole_number'];
						$rc_point = 0;
						if ($tournament_id > 0) {
							if ($row['hole_par'] >= $scores[$h]['score_value'] && $scores[$h]['score_value'] > 0) {
								$rc_point = $row['hole_par'] - $scores[$h]['score_value'] + 1;
								$rc_points += $rc_point;
							}
						}
						$hrow .= '<td>'.$h.'<input type="hidden" name="score_id_'.$h.'" value="'.(isset($scores[$h])?$scores[$h]['score_id']:0).'"/>'.
							'<input type="hidden" name="hole_id_'.$h.'" value="'.$row['hole_id'].'"/><input type="hidden" name="hole_distance_'.$h.'" value="'.$row['hole_distance'].'"/></td>';
						$parrow .= '<td>'.$row['hole_par'].'<input type="hidden" name="hole_par_'.$h.'" value="'.$row['hole_par'].'"/></td>';
						$srow .= '<td><input type="text" name="score_value_'.$h.'" value="'.(isset($scores[$h])?$scores[$h]['score_value']:0).'" size="3" maxlength="2" onChange="calcTots()"/></td>';
						$prow .= '<td><input type="text" name="score_putts_'.$h.'" value="'.(isset($scores[$h])?$scores[$h]['score_putts']:0).'" size="3" maxlength="1"/></td>';
						$drow .= '<td><input type="text" name="score_drive_distance_'.$h.'" value="'.(isset($scores[$h])?$scores[$h]['score_drive_distance']:0).'" size="3" maxlength="3"/></td>';
						$frow .= '<td><input type="checkbox" name="score_fairway_'.$h.'" value="1" '.(isset($scores[$h])?($scores[$h]['score_fairway']?'checked':''):'').'/></td>';
						$grow .= '<td><input type="checkbox" name="score_gir_'.$h.'" value="1" '.(isset($scores[$h])?($scores[$h]['score_gir']?'checked':''):'').'/></td>';
						$rcrow .= '<td>'.($rc_point?$rc_point:'&nbsp;').'</td>';
						$chrow .= '<td>&nbsp;</td>';
						$hcprow .= '<td>'.$row['hole_handicap'].'</td>';
						if ($h > 9) {
							$in += $row['hole_par'];
							$s_in += isset($scores[$h])?$scores[$h]['score_value']:0;
						} else {
							$out += $row['hole_par'];
							$s_out += isset($scores[$h])?$scores[$h]['score_value']:0;
						}
						if ($h == 9) {
							$parrow .= '<td>'.$out.'</td>';
							$hrow .= '<td>Out</td>';
							$srow .= '<td><b><span id="out_score_value">'.$s_out.'</span></b></td>';
							$prow .= '<td rowspan="7">&nbsp;</td>';
						}
						if (isset($scores[$h])) {
							if ($scores[$h]['score_drive_distance'] > 0) {
								$drive_avg_holes++;
								$drive_distance += $scores[$h]['score_drive_distance'];
							}
							$fairway += $scores[$h]['score_fairway'];
							$green += $scores[$h]['score_gir'];
							$putts += $scores[$h]['score_putts'];
						}
					}
					cal_points( 0, $round_row['round_date'], $tournament_id, $rc_points, $round_row['signup_gross_standing'], $round_row['signup_net_standing'], $ryder_points, $champion_points );
					$hrow .= '<td>In</td><td>Total</td></tr>';
					$parrow .= '<td></b>'.$in.'<b></td><td>'.($in + $out).'</td></tr>';
					$srow .= '<td><b><span id="in_score_value">'.$s_in.'</span></b></td><td><b><span id="total_score_value">'.($s_in + $s_out).'</span></b></td></tr>';
					$prow .= '<td nowrap>tot:'.$putts.'<br>avg:'.sprintf('%.2f', $green>0?$putts/18:'n/a').' (if GIR)</td><td nowrap><input type="checkbox" name="score_is_par" value="1"/>score is &#177; par</td></tr>';
					$drow .= '<td>'.($drive_avg_holes?sprintf('%.1f',$drive_distance/$drive_avg_holes) : '&nbsp;').'</td><td nowrap><input type="checkbox" name="distance_to_flag" value="1"/>distance is to flag</td></tr>';
					$frow .= '<td colspan="2">'.$fairway.'</td></tr>';
					$grow .= '<td colspan="2">'.$green.'</td></tr>';
					$havgrow .= '<td colspan="9">Par 5s: '.sprintf('%.1f', $round_row['round_par5']).
							', Par 4s: '.sprintf('%.1f', $round_row['round_par4']).
							', Par 3s: '.sprintf('%.1f', $round_row['round_par3']).'</td></tr>';
					$rcrow .= '<td colspan="2">';
					foreach ($ryder_points as $k => $v) {
						$rcrow .= $k.':'.$v . ' ';
					}
					$rcrow .= '</td></tr>';
					$chrow .= '<td colspan="2">';
					foreach ($champion_points as $k => $v) {
						$chrow .= $k.':'.$v . ' ';
					}
					$chrow .= '</td></tr>';
					$hcprow .= '<td colspan="2">&nbsp;</td></tr>';
					$s .= $hrow.$parrow.$srow.$prow.$drow.$frow.$grow.$havgrow.$rcrow.$chrow.$hcprow.'</table>';
					$s .= '<td>&nbsp;</td></tr>';
				} else {
					$s .= '<td colspan="2">No hole info, please add hole details in course info to enter hole-by-hole</td>';
				}
				$s .= '<tr><td colspan="3" align="center"><input type="submit" value="'.($round_id?'update':'add').'"/></td></tr>';
			}
		// set form action depending state
		$s = '<form method="post" action="'.$form_action.'&user_id='.$player_id.
			'&signup_id='.$signup_id.'&tournament_id='.$tournament_id.'&course_id='.$course_id.
			'&teebox_id='.$teebox_id.'&start_over='.$start_over.'&round_id='.$round_id.'"/>'.
			$s;

		$s .= '</table></td></tr>';
		$s .= '</form>';
	}
	if ($_GET['user_id']) {
		$result = $db->sql_query('select username from '.$user_prefix.'_users where user_id='.$_GET['user_id']);
		if ($db->sql_numrows($result)) {
			$row = $db->sql_fetchrow($result);
			$user_name = $row['username'];
		}
	}
	$s .= '<tr><td><table align="center"><tr><td><b>'.(isset($user_name)?strtoupper($user_name).' Handicap/Scores':'Who Have Played Here').'</b></td></tr></table></td></tr>';
	$s .= '<tr><td align="center">'.scores_show().'</td></tr>';

	$s .= '</table>';

	echo $s;

}
function players_scores_addedit() {
	global $db, $user_prefix, $admin, $user, $user_info;

	// score info
	$round_added_by = $_POST['round_added_by'];
	$round_id = $_POST['round_id'];
	$signup_id = $_POST['signup_id']?$_POST['signup_id']:0;
	$user_id = $_POST['user_id']?$_POST['user_id']:0;
	$round_score = $_POST['round_score']?$_POST['round_score']:0;
	if ($round_id > 0) {
		$sql = 'update '.$user_prefix.'_golf_rounds set round_date="'.FixQuotes($_POST['round_date']).'",
			user_id="'.$_POST['user_id'].'", signup_id="'.$_POST['signup_id'].'",
			tournament_id="'.$_POST['tournament_id'].'",
			teebox_id="'.$_POST['teebox_id'].'",
			round_score="'.$round_score.'" where round_id="'.$round_id.'"';
	} else {
		$sql = 'insert into '.$user_prefix.'_golf_rounds (round_date, user_id, signup_id, tournament_id,
			teebox_id, round_added_by, round_score) values ("'.FixQuotes($_POST['round_date']).'","'.$user_id.'","'.$signup_id.'",
			"'.$_POST['tournament_id'].'","'.$_POST['teebox_id'].'","'.$_POST['round_added_by'].'","'.FixQuotes($round_score).'")';
	}
	//echo '<pre>'.$sql.'</pre>';
	$result = $db->sql_query($sql);
	if(!$result) {
		echo ""._ERROR." $sql<br>";
		exit();
	}
	if (!$round_id) {
		$round_id = $db->sql_nextid();
	}
	//echo '<pre>'.$sql.'</pre>';
	$round_ryder_points=0;
	$round_putts = 0; $round_fairways = 0; $round_girs = 0;
	$round_par_scores = array(3=>0, 4=>0, 5=>0);
	$round_par_counts = array(3=>0, 4=>0, 5=>0);
	if ($round_score == 0) {
		// hole score
		$score_is_to_par = $_POST['score_is_par'];
		$distance_is_to_flag = $_POST['distance_to_flag'];
		for ($h=1;$h<=18;$h++) {

			$score_drive_distance = $_POST['score_drive_distance_'.$h]?$_POST['score_drive_distance_'.$h]:0;
			if ($distance_is_to_flag && $score_drive_distance > 0) {
				$score_drive_distance = $_POST['hole_distance_'.$h] - $score_drive_distance;
			}

			$score_value = $_POST['score_value_'.$h]?$_POST['score_value_'.$h]:0;
			$this_hole_score = $score_value;
			if ($score_is_to_par) {
				$score_value += $_POST['hole_par_'.$h];
				$this_hole_score += $_POST['hole_par_'.$h];
			}

			$score_id = $_POST['score_id_'.$h];
			if ($score_id > 0) {
				$sql = 'update '.$user_prefix.'_golf_scores set score_value="'.FixQuotes($score_value).'", score_fairway="'.FixQuotes($_POST['score_fairway_'.$h]).'",
					 score_drive_distance="'.FixQuotes($score_drive_distance).'", score_gir="'.FixQuotes($_POST['score_gir_'.$h]).'",
					 score_putts="'.FixQuotes($_POST['score_putts_'.$h]).'", hole_id="'.FixQuotes($_POST['hole_id_'.$h]).'", round_id="'.$round_id.'" where score_id="'.$score_id.'"';
			} else {
				$sql = 'insert into '.$user_prefix.'_golf_scores (score_value, score_fairway, score_drive_distance, score_gir, score_putts, hole_id, round_id)
				values ("'.FixQuotes($score_value).'","'.FixQuotes($_POST['score_fairway_'.$h]).'","'.FixQuotes($score_drive_distance).'",
				"'.FixQuotes($_POST['score_gir_'.$h]).'","'.FixQuotes($_POST['score_putts_'.$h]).'","'.FixQuotes($_POST['hole_id_'.$h]).'","'.$round_id.'")';
			}
			//echo '<pre>'.$sql.'</pre>';
			$result = $db->sql_query($sql);
			$round_score += $score_value;
			$round_fairways += $_POST['score_fairway_'.$h];
			$round_girs += $_POST['score_gir_'.$h];
			$round_putts += $_POST['score_putts_'.$h];
			$round_par_scores[$_POST['hole_par_'.$h]] += $this_hole_score;
			$round_par_counts[$_POST['hole_par_'.$h]]++;

			if ($_POST['tournament_id']) {
				// calculate ryder cup points, based on hole scores only, totals for all rounds will be calculated on tournament standing and other point rules
				$rc_point = $_POST['hole_par_'.$h] - $score_value;
				if ($rc_point >= 0 &&  $score_value > 0) {
					$round_ryder_points += $rc_point + 1;
				}
			}
		}
	}
	$sql = 'update '.$user_prefix.'_golf_rounds set round_score_temp="'.FixQuotes($round_score).'",
		round_fairways="'.FixQuotes($round_fairways).'",
		round_girs="'.FixQuotes($round_girs).'",
		round_putts="'.FixQuotes($round_putts).'",
		round_par3="'.FixQuotes($round_par_counts[3]>0?$round_par_scores[3]/$round_par_counts[3]:0).'",
		round_par4="'.FixQuotes($round_par_counts[4]>0?$round_par_scores[4]/$round_par_counts[4]:0).'",
		round_par5="'.FixQuotes($round_par_counts[5]>0?$round_par_scores[5]/$round_par_counts[5]:0).'",
		round_ryder_points="'.FixQuotes($round_ryder_points).'"
		where round_id="'.$round_id.'" limit 1';
	$result = $db->sql_query($sql);
	//echo $sql; exit();
	header('Location: /modules.php?name=Golf&op=players_scores&user_id='.$_POST['user_id'].'&signup_id='.$_POST['signup_id']);

}
function players_scores_delete() {
	global $db, $user_prefix, $admin;
	if(!$admin) {
		echo '<span class="error">Admin only!</span>';
		return;
	}
	$round_id = $_GET['round_id'];
	if ($round_id > 0) {
		$sql = 'delete from '.$user_prefix.'_golf_scores where round_id='.$round_id.' limit 18';
		$result = $db->sql_query($sql);
		$sql = 'delete from '.$user_prefix.'_golf_rounds where round_id='.$round_id.' limit 1';
		$result = $db->sql_query($sql);
	}
	header('Location: /modules.php?name=Golf&op=players_scores&user_id='.$_GET['user_id']);
}

function players_stats() {
	$s = '<table align="center">';
	$s .= '<tr><td><table align="center"><tr><td><b>Players/Stats</b></td></tr></table></td></tr>';

	$s .= '<tr><td>Full stats coming soon</td></tr>';

	$s .= '</table>';

	echo $s;
}

function players_tournaments() {

}
?>
