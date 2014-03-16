<?php
/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2005 by M. Pranivong (tournaments@dfwlga.com)          */
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

function tournaments_menu() {
	$s = '<table align="center"><tr><td>';
	$s .= '[<a href="?name=Golf&op=tournaments_rules">Rules</a>]';
	$s .= '</td></tr></table>';
	echo $s;
}
function tournaments() {
	global $db, $user_prefix, $admin, $golf_config;
	global $hole_contests;
	
	$tournament_id=$_GET['tournament_id'];

	$holes = array(0=>'');
	for ($i=1;$i<19;$i++) { $holes[$i] = $i; }
	
	// retrieve course list
	$course_list = array(0 => '');
    $result = $db->sql_query('select course_id, course_name from '. $user_prefix . '_golf_courses order by course_name');
    if ($db->sql_numrows($result)) {
		while ($row = $db->sql_fetchrow($result)) {
			$course_list[$row['course_id']] = ui_form_safe($row['course_name']);
		}
	}
	
	$s = '<table align="center">';
	
	$s .= '<tr><td><table align="center"><tr><td><b>Tournaments</b></td></tr></table></td></tr>';

	// tournament year navigation
	$sql = 'select distinct(year(tournament_date)) as tournament_year from '.$user_prefix.'_golf_tournaments group by tournament_id order by year(tournament_date) DESC';
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$s .= '<tr><td><table align="center"><tr><td>';
		while ($row = $db->sql_fetchrow($result)) {
			if ($golf_config['tournament_show_year'] == $row['tournament_year']) {
				$s .= '['.$row['tournament_year'].']&nbsp;';
			} else {
				$s .= '[<a href="/modules.php?name=Golf&op=tournaments&tournament_year='.$row['tournament_year'].'">'.$row['tournament_year'].'</a>]&nbsp;';
			}
		}
		$s .= '</td></tr></table></td></tr>';
	}
	
	$s .= '<tr><td><table border="1px solid black" cellpadding=4 cellspacing=0 align="center">';
	$s .= '<tr><td><b>Tournament</b></td><td><b>Date</b></td><td><b>Sign Up</b></td><td><b>Results</b></td><td><b>Photos</b></td>'.($admin?'<td>&nbsp;</td><td>&nbsp;</td>':'').'</tr>';
	
	$sql = 'select tournament_results, tournament_id, tournament_name, UNIX_TIMESTAMP(tournament_date) as tournament_date, 
		course_name, course_url, UNIX_TIMESTAMP(tournament_deadline) tournament_deadline, tournament_photo_url 
		from '.$user_prefix.'_golf_tournaments gt 
		left join '.$user_prefix.'_golf_courses gc on gc.course_id=gt.course_id 
		left join '.$user_prefix.'_golf_course_teeboxes gct on gct.teebox_id=gt.teebox_id ';
	if ($golf_config['tournament_show_year']) {
		$sql .= 'where year(tournament_date) = '. $golf_config['tournament_show_year'];
	}
	
	$sql .= ' order by tournament_date';
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$today = time();
		while ($row = $db->sql_fetchrow($result)) {
			$tournament_date = $row['tournament_date'];
			$tournament_deadline = $row['tournament_deadline'];
			$s .= '<tr>
			<td><a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">'.$row['tournament_name'].'</a>&nbsp;</td>
			<td>'.($today>$tournament_date?'<s>':'').date('D, M j, Y g:ia',$tournament_date).'&nbsp;';
			if ($today <= $tournament_deadline) {
				$s .= '<br>(deadline '.date('D, M j, Y',$tournament_deadline).')';
			}
			$s .= ($today>$tournament_date?'</s>':'').'</td>';
			$s .= '<td>'.($today<=$tournament_deadline?'<a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">sign up</a>':'&nbsp;').'</td>';
			
			$result_html='';
			if ($today >= $tournament_date) {
				$result_html = '<a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">results</a>';
			}
			$s .= '<td>'.$result_html . '&nbsp;</td>';
			
			$photo_dir = getcwd() . '/modules/Golf/photos/'.$row['tournament_id'];
			$spg_file = getcwd() . '/modules/Golf/photos/sp_index.php';
			$photo_html='';
			if ($today >= $tournament_date) {
				if (strlen($row['tournament_photo_url'])) {
					$photo_html = '<a href="'.$row['tournament_photo_url'].'" target="_photo">photos</a>';
				} else if (strlen($golf_config['photo_url'])) {
					$photo_html = '<a href="'.$golf_config['photo_url'].'" target="_photo">photos</a>';
				} else {
					$photo_html = '<a href="javascript:alert(\'See admin for details.\');">not supported</a>';
				}
			}
			$s .= '<td>'.$photo_html.'&nbsp;</td>'. 
			($admin?'<td><a href="/modules.php?name=Golf&op=tournaments&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">edit</a></td>':'').
			($admin?'<td><a href="/modules.php?name=Golf&op=tournaments_delete&tournament_id='.$row['tournament_id'].'&tournament_year='.$golf_config['tournament_show_year'].'">delete</a></td>':'').
			'</tr>';
		}
	}
	
	$s .= '</td></tr></table></td></tr>';
	$teebox_list = array(0 => '');
	if ($tournament_id) {
		$sql = 'SELECT *, UNIX_TIMESTAMP(tournament_date) tournament_date,
			UNIX_TIMESTAMP(tournament_deadline) tournament_deadline FROM '.$user_prefix.'_golf_tournaments gt 
			LEFT JOIN '.$user_prefix.'_golf_courses gc ON gc.course_id=gt.course_id WHERE tournament_id='.$tournament_id;
		$result = $db->sql_query($sql);
		if ($db->sql_numrows($result)) {
			$edit_row = $db->sql_fetchrow($result);
			$tournament_date = $edit_row['tournament_date'];
			$tournament_deadline = $edit_row['tournament_deadline'];
			
			// retrieve teebox list
			$sql='select teebox_id, teebox_name from '. $user_prefix . '_golf_course_teeboxes where course_id='.$edit_row['course_id'].' order by teebox_slope DESC';
			$result = $db->sql_query($sql);
			if ($db->sql_numrows($result)) {
				while ($row = $db->sql_fetchrow($result)) {
					$teebox_list[$row['teebox_id']] = ui_form_safe($row['teebox_name']);
				}
			}
		}
	}
	if ($admin) {
		$flights_count = 0;
		$flight_list = array(0=>'');
		if (isset($edit_row)) {
			$sql = 'select * from '.$user_prefix.'_golf_tournament_flights where tournament_id='.$edit_row['tournament_id'].' order by flight_id';
			$result = $db->sql_query($sql);
			$flights_count = $db->sql_numrows($result);
		}
		$s .= '<tr><td><table align="center"><tr><td><b>'.(isset($edit_row)?'Edit':'Add').' Tournament</b></td></tr></table></td></tr>';
		$s .= 
		'<tr><td>
		<form name="tournaments" action="/modules.php?name=Golf&op=tournaments_addedit&tournament_id='.$tournament_id.'&tournament_year='.$golf_config['tournament_show_year'].'" method="POST"/>
		<input type="hidden" name="tournament_id" value="'.$tournament_id.'"/>
		<table align="center">
			<tr><td align="right">Name:</td><td><input type="text" name="tournament_name" value="'.(isset($edit_row)?ui_form_safe($edit_row['tournament_name']):'').'" size="60" maxlength="100"/></td></tr>
			<tr><td align="right">Date:</td><td><input type="text" name="tournament_date" value="'.(isset($edit_row)?date('Y-m-d H:i',$tournament_date):'').'" size="30" maxlength="30"/>&nbsp;ex:yyyy-mm-dd hh:mm</td></tr>
			<tr><td align="right">Deadline:</td><td><input type="text" name="tournament_deadline" value="'.(isset($edit_row)?date('Y-m-d H:i',$tournament_deadline):'').'" size="30" maxlength="30"/>&nbsp;ex:yyyy-mm-dd hh:mm</td></tr>
			<tr><td align="right">Maximum Players:</td><td><input type="text" name="tournament_max_players" value="'.(isset($edit_row)?$edit_row['tournament_max_players']:'').'" size="4" maxlength="3"/></td></tr>
			<tr><td align="right">Cost (member) $</td><td><input type="text" name="tournament_cost_member" value="'.(isset($edit_row)?sprintf('%.2f',$edit_row['tournament_cost_member']):'').'" size="20" maxlength="30"/>pp (-1=TBD)</td></tr>
			<tr><td align="right">Cost (non-member, golf only) $</td><td><input type="text" name="tournament_cost_nonmember" value="'.(isset($edit_row)?sprintf('%.2f',$edit_row['tournament_cost_nonmember']):'').'" size="20" maxlength="30"/>pp (-1=TDB)</td></tr>
			<tr><td align="right">Course</td><td>'.ui_select_box($course_list, 'course_id', (isset($edit_row)?$edit_row['course_id']:0) ) . '</td></tr>
			<tr><td align="right">Course Tee Box</td><td>'.ui_select_box($teebox_list, 'teebox_id', (isset($edit_row)?$edit_row['teebox_id']:0) ) . ' (first select a golf course and save to select a tee box)</td></tr>
			<tr><td align="right">Format</td><td><input type="text" name="tournament_format" value="'.(isset($edit_row)?$edit_row['tournament_format']:'').'" size="60" maxlength="64"/></td></tr>
			<tr><td align="right">Non-Member Winner:</td><td><input type="checkbox" name="tournament_nonmember_winner" value="1" '.(isset($edit_row)?($edit_row['tournament_nonmember_winner']?'checked':''):'').'/> (prizes open to non-members)</td></tr>
			<tr><td align="right">Flights</td><td nowrap><input type="text" name="flights" value="'.$flights_count.'" size="2" maxlength="1"/> save tournament info first to edit flight details</td></tr>
			<tr><td align="right" valign="top">Flights Details:</td>
				<td>
					<table cellpadding="0" style="border: solid 1px;">';
					if ($flights_count) {
						$n_row = '<tr><td align="right">Flight:</td>';
						$h_row = '<tr><td align="right">Max HCP:</td>';
						$t_row = '<tr><td align="right">Teebox:</td>';
						$net_row = '<tr><td align="right">Net:<br># of awards</td>';
						$gross_row = '<tr><td align="right" valign="top">Gross:<br># of awards</td>';
						$f = 0;
						while ($row = $db->sql_fetchrow($result)) {
							$f++;
							$n_row .= '<td nowrap><input type="hidden" name="flight_id_'.$f.'" value="'.$row['flight_id'].'"/><input type="text" name="flight_name_'.$f.'" value="'.$row['flight_name'].'" size="10" maxlength="32"/></td>';
							$h_row .= '<td nowrap><input type="text" name="flight_max_hcp_'.$f.'" value="'.$row['flight_max_hcp'].'" size="4" maxlength="4"/></td>';
							$t_row .= '<td nowrap>'.ui_select_box($teebox_list, 'teebox_id_'.$f, $row['teebox_id']).'</td>';
							$net_row .= '<td nowrap valign="top"><input type="text" name="flight_net_awards_'.$f.'" value="'.$row['flight_net_awards'].'" size="2" maxlength="1"/></td>';
							$gross_row .= '<td nowrap valign="top"><input type="text" name="flight_gross_awards_'.$f.'" value="'.$row['flight_gross_awards'].'" size="2" maxlength="1"/></td>';
							$flight_list[$row['flight_id']] = ui_form_safe($row['flight_name']);
						}
						$n_row .= '</tr>';
						$h_row .= '</tr>';
						$t_row .= '</tr>';
						$net_row .= '</tr>';
						$gross_row .= '</tr>';
						$s .= $n_row.$h_row.$t_row.$net_row.$gross_row;
					} else {
						$s .= '<tr><td>None</td></tr>';
					}
			$s .='
					</table>
				&nbsp;</td>
			</tr>';
			
			$side_contest_count = 0;
			if (isset($edit_row)) {
				$sql = 'select * from '.$user_prefix.'_golf_tournament_side_contests where tournament_id='.$edit_row['tournament_id'].' order by hole_id, contest_id';
				$result = $db->sql_query($sql);
				$side_contest_count = $db->sql_numrows($result);
			}
			
			$s .= '
			<tr><td align="right">Side Contests</td><td nowrap><input type="text" name="side_contests" value="'.$side_contest_count.'" size="4" maxlength="2"/> save tournament info first to edit contest details</td></tr>
			<tr><td align="right" valign="top">Side Contest Holes</td>
				<td>
					<table cellpadding="0" style="border: solid 1px;">';
					if ($side_contest_count) {
						
						$signups = array(0=>'');
						$sql = 'select signup_id, username, signup_name from '.$user_prefix.'_golf_tournament_signups gts 
						left join '.$user_prefix.'_users u on u.user_id=gts.user_id 
						where gts.tournament_id='.$tournament_id . ' order by username, signup_name';
						$sresult = $db->sql_query($sql);
						if ($db->sql_numrows($sresult)) {
							while ($row = $db->sql_fetchrow($sresult)) {
								$signups[$row['signup_id']] = ui_form_safe($row['username']?$row['username']:$row['signup_name']);
							}
						}
						
						$holes = array(0=>'');
						$sql = 'select hole_id, concat(\'#\',hole_number, \' - Par \', hole_par) hole_desc from '.$user_prefix.'_golf_holes 
							where teebox_id='.$edit_row['teebox_id'].' order by hole_number';
						$hresult = $db->sql_query($sql);
						if ($db->sql_numrows($hresult)) {
							while ($row = $db->sql_fetchrow($hresult)) {
								$holes[$row['hole_id']] = ui_form_safe($row['hole_desc']);
							}
						}
						$h_row = '<tr><td align="right">Hole:</td>';
						$c_row = '<tr><td align="right">Contest:</td>';
						$f_row = '<tr><td align="right">Flight:</td>';
						$w_row = '<tr><td align="right">Winner:</td>';
						$n_row = '<tr><td align="right">Note:</td>';
						$h = 0;
						while ($row = $db->sql_fetchrow($result)) {
							$h++;
							$h_row .= '<td nowrap>'.ui_select_box($holes, 'hole_id_'.$h, $row['hole_id']) .'<input type="hidden" name="contest_id_'.$h.'" value="'.$row['contest_id'].'"/></td>';
							$c_row .= '<td nowrap>'.ui_select_box($hole_contests, 'contest_type_'.$h, $row['contest_type']) .'</td>';
							$f_row .= '<td nowrap>'.ui_select_box($flight_list, 'c_flight_id_'.$h, $row['flight_id']) .'</td>';
							$w_row .= '<td nowrap>'.ui_select_box($signups, 'winner_signup_id_'.$h, $row['winner_signup_id']) .'</td>';
							$n_row .= '<td nowrap><input type="text" name="contest_note_'.$h.'" value="'.ui_form_safe($row['contest_note']) .'" size="12" maxlength="10"/></td>';
						}
						$h_row .= '</tr>';
						$c_row .= '</tr>';
						$f_row .= '</tr>';
						$w_row .= '</tr>';
						$n_row .= '</tr>';
						$s .= $h_row.$c_row.$f_row.$w_row.$n_row;
					} else {
						$s .= '<tr><td>None</td></tr>';
					}
			$s .= '
					</table>
				&nbsp;</td>
			</tr>
			<tr><td align="right"># of Partners</td><td><input type="text" name="tournament_partner_require" value="'.(isset($edit_row)?$edit_row['tournament_partner_require']:'').'" size="5" maxlength="5"/></td></tr>
			<tr><td align="right">Mulligan</td><td><input type="text" name="tournament_mulligan" value="'.(isset($edit_row)?$edit_row['tournament_mulligan']:'').'" size="60" maxlength="64"/></td></tr>
			<tr><td align="right">Prizes</td><td><input type="text" name="tournament_prizes" value="'.(isset($edit_row)?$edit_row['tournament_prizes']:'').'" size="60" maxlength="100"/></td></tr>
			<tr><td align="right" valign="top">Results</td><td><textarea name="tournament_results" cols="60" rows="6"/>'.(isset($edit_row)?$edit_row['tournament_results']:'').'</textarea></td></tr>
			<tr><td align="right" valign="top">Note</td><td><textarea name="tournament_note" cols="60" rows="4">'.(isset($edit_row)?$edit_row['tournament_note']:'').'</textarea></td></tr>
			<tr><td align="right">Active:</td><td><input type="checkbox" name="tournament_active" value="1" '.(isset($edit_row)?($edit_row['tournament_active']?'checked':''):'').'/></td></tr>
			<tr><td align="right">Photo URL</td><td><input type="text" name="tournament_photo_url" value="'.(isset($edit_row)?$edit_row['tournament_photo_url']:'').'" size="100" maxlength="200"/></td></tr>
			<tr><td align="right"></td><td><input type="submit" value="'.(isset($edit_row)?'update':'add').'"/></td></tr>
		</table>
		</form>
		</td></tr>';
	} else {
		if (isset($edit_row)) {
			$s .= '<tr><td><table align="center"><tr><td><b>'.ui_form_safe($edit_row['tournament_name']). ' Results</b></td></tr></table></td></tr>';
			$results = $edit_row['tournament_results'];
			if (strlen($results) == 0) {
				$results = 'coming soon';
			}
			$s .= '<tr><td><table align="center"><tr><td><b>'.nl2br($results).'</b></td></tr></table></td></tr>';
		}
	}
	$s .= '</table>';
	echo $s;
	
}
function tournaments_addedit() {
	global $admin, $db, $user_prefix;
	if (!$admin) { echo 'Access denied, admin only!'; return; }
	
	$tournament_id=$_POST['tournament_id'];
	$tournament_date = $_POST['tournament_date'];
	$tournament_deadline = $_POST['tournament_deadline'];
	
	if ($tournament_id) {
		$sql = 'UPDATE '.$user_prefix.'_golf_tournaments SET
		tournament_name="'.FixQuotes($_POST['tournament_name']).'",
		tournament_date="'.$tournament_date.'",
		tournament_deadline="'.$tournament_deadline.'",
		tournament_max_players="'.FixQuotes($_POST['tournament_max_players']).'",
		tournament_cost_member="'.FixQuotes($_POST['tournament_cost_member']).'",
		tournament_cost_nonmember="'.FixQuotes($_POST['tournament_cost_nonmember']).'",
		tournament_format="'.FixQuotes($_POST['tournament_format']).'",
		tournament_nonmember_winner="'.FixQuotes($_POST['tournament_nonmember_winner']).'",
		tournament_partner_require="'.FixQuotes($_POST['tournament_partner_require']).'",
		tournament_mulligan="'.FixQuotes($_POST['tournament_mulligan']).'",
		tournament_prizes="'.FixQuotes($_POST['tournament_prizes']).'",
		tournament_note="'.FixQuotes($_POST['tournament_note']).'",
		tournament_results="'.FixQuotes($_POST['tournament_results']).'",
		course_id="'.FixQuotes($_POST['course_id']).'",
		teebox_id="'.FixQuotes($_POST['teebox_id']).'",
		tournament_photo_url="'.FixQuotes($_POST['tournament_photo_url']).'"
		WHERE tournament_id='.$tournament_id;
	} else {
		$sql = 'INSERT INTO '.$user_prefix.'_golf_tournaments (tournament_name, tournament_date, tournament_deadline, tournament_cost_member, tournament_cost_nonmember,
		tournament_format, tournament_nonmember_winner, tournament_partner_require, tournament_mulligan, tournament_prizes, 
		tournament_max_players, tournament_results, tournament_note, course_id, teebox_id, tournament_photo_url)
		VALUES ("'.FixQuotes($_POST['tournament_name']).'","'.$tournament_date.'","'.$tournament_deadline.'",
		"'.FixQuotes($_POST['tournament_cost_member']).'","'.FixQuotes($_POST['tournament_cost_nonmember']).'","'.FixQuotes($_POST['tournament_format']).'",
		"'.FixQuotes($_POST['tournament_nonmember_winner']).'","'.FixQuotes($_POST['tournament_partner_require']).'","'.FixQuotes($_POST['tournament_mulligan']).'",
		"'.FixQuotes($_POST['tournament_prizes']).'","'.FixQuotes($_POST['tournament_max_players']).'","'.FixQuotes($_POST['tournament_results']).'",
		"'.FixQuotes($_POST['tournament_note']).'","'.FixQuotes($_POST['course_id']).'","'.FixQuotes($_POST['teebox_id']).'","'.FixQuotes($_POST['tournament_photo_url']).'")';
		$tournament_id = $db->sql_nextid();
	}
	$result = $db->sql_query($sql);
    if(!$result) {
    	echo ""._ERROR." $sql<br>";
		exit();
    }
	// save flights info
	$flights_count = $_POST['flights'];
	for ($f=1; $f<=$flights_count; $f++) {
		$flight_id = $_POST['flight_id_'.$f];
		if ($flight_id > 0) {
			$sql = 'update '.$user_prefix.'_golf_tournament_flights set flight_name="'.FixQuotes($_POST['flight_name_'.$f]).'",
				flight_max_hcp="'.FixQuotes($_POST['flight_max_hcp_'.$f]).'",
				flight_net_awards="'.FixQuotes($_POST['flight_net_awards_'.$f]).'",
				flight_gross_awards="'.FixQuotes($_POST['flight_gross_awards_'.$f]).'",
				teebox_id="'.FixQuotes($_POST['teebox_id_'.$f]).'" where flight_id='.$_POST['flight_id_'.$f];
		} else {
			$sql = 'insert into '.$user_prefix.'_golf_tournament_flights (flight_name, tournament_id) values ("'.chr($f+64).'","'.$tournament_id.'")';
		}
		$result = $db->sql_query($sql);
		//echo '<pre>'.$sql.'</pre>';
	}
	while (true) {
		$flight_id = $_POST['flight_id_'.$f];
		if ($flight_id > 0) {
			$sql = 'delete from '.$user_prefix.'_golf_tournament_flights where flight_id='.$flight_id;
			$result = $db->sql_query($sql);
		} else {
			break;
		}
		$f++;
	}

	// save side contest info
	$side_contest_count = $_POST['side_contests'];
	for ($c=1; $c<=$side_contest_count; $c++) {
		$contest_id = $_POST['contest_id_'.$c];
		if ($contest_id > 0) {
			$sql = 'update '.$user_prefix.'_golf_tournament_side_contests set contest_type="'.FixQuotes($_POST['contest_type_'.$c]).'",
				flight_id="'.FixQuotes($_POST['c_flight_id_'.$c]).'",tournament_id="'.FixQuotes($tournament_id).'",
				hole_id="'.FixQuotes($_POST['hole_id_'.$c]).'",winner_signup_id="'.FixQuotes($_POST['winner_signup_id_'.$c]).'",
				contest_note="'.FixQuotes($_POST['contest_note_'.$c]).'" 
				where contest_id='.$_POST['contest_id_'.$c];
		} else {
			$sql = 'insert into '.$user_prefix.'_golf_tournament_side_contests (tournament_id) values ("'.$tournament_id.'")';
		}
		$result = $db->sql_query($sql);
		//echo '<pre>'.$sql.'</pre>';
	}
	while (true) {
		$side_contest_id = $_POST['contest_id_'.$c];
		if ($side_contest_id > 0) {
			$sql = 'delete from '.$user_prefix.'_golf_tournament_side_contests where contest_id='.$contest_id;
			$result = $db->sql_query($sql);
		} else {
			break;
		}
		$c++;
	}
	
	
	header('Location: /modules.php?name=Golf&op=tournaments&tournament_id='.$tournament_id.($_GET['tournament_year']?'&tournament_year='.$_GET['tournament_year']:'') );
}
function tournaments_delete() {
	global $admin, $user_prefix, $db;
	if (!$admin) { echo 'Access denied, admin only!'; return; }

	$tournament_id = $_GET['tournament_id'];
	if ($tournament_id) {
		//TODO: check if tournament has dependencies
		$sql = 'DELETE FROM '.$user_prefix.'_golf_tournaments WHERE tournament_id='.$tournament_id.' LIMIT 1';
		$result = $db->sql_query($sql);
		if (!$result) {
			if ($admin) {
				echo ""._ERROR."<br>";
				exit();
			}
		}
	}
	header('Location: /modules.php?name=Golf&op=tournaments'.($_GET['tournament_year']?'&tournament_year='.$_GET['tournament_year']:'') );
}
function tournaments_photos() {
	echo 'coming soon';
}
function tournaments_results() {
	echo 'coming soon';
}

function tournaments_signup() {
	global $admin, $user_prefix, $db, $golf_config;
	global $hole_contests;
	
	$tournament_id=$_GET['tournament_id'];
	$signup_id=$_GET['signup_id'];
	$teebox_list = array(0=>'');
	
	$flight_list = array();
	$flight_hcp = array();
	
	$today = time();
	
	$active_count = 0;
	
	$html = '';
	$s = '<table align="center">';
	$s .= '<tr><td><table align="center"><tr><td><b>Tournament Info/Signup</b></td></tr></table></td></tr>';

	$players = array(0=>''); $players_handicap = array(0=>0); $players_member = array(0=>0);
	$sql = 'select u.user_id, username, player_handicap_temp, player_member from '.$user_prefix.'_users u 
		left join '.$user_prefix.'_golf_players p on p.user_id=u.user_id 
		order by username';
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		while ($row = $db->sql_fetchrow($result)) {
			 $players[$row['user_id']] = ui_form_safe($row['username']);
			 $players_handicap[$row['user_id']] = (isset($row['player_handicap_temp'])?$row['player_handicap_temp']:0);
			 $players_member[$row['user_id']] = (isset($row['player_member'])?$row['player_member']:0);
		}
	}
	
	if ($tournament_id) {
		$sql = 'SELECT gt.*, UNIX_TIMESTAMP(tournament_date) tournament_date, tournament_partner_require,
			UNIX_TIMESTAMP(tournament_deadline) tournament_deadline, teebox_name, gc.*
			FROM '.$user_prefix.'_golf_tournaments gt 
			LEFT JOIN '.$user_prefix.'_golf_courses gc ON gc.course_id=gt.course_id 
			LEFT JOIN '.$user_prefix.'_golf_course_teeboxes gct ON gct.teebox_id=gt.teebox_id
			WHERE tournament_id='.$tournament_id;
		$result = $db->sql_query($sql);		
		if ($db->sql_numrows($result)) {
			$trow = $db->sql_fetchrow($result);
			
			$member_paypal = '<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
								<input type="hidden" name="cmd" value="_xclick">
								<input type="hidden" name="business" value="'.ui_form_safe($golf_config['paypal_email']).'">
								<input type="hidden" name="currency_code" value="USD">
								<input type="hidden" name="amount" value="'.sprintf('%.2f',$trow['tournament_cost_member']).'">
								<input type="hidden" name="item_name" value="DFWLGA tournament entry fee (member): %s">';
								if (strlen($golf_config['paypay_return_url'])) {
									$s .= '<input type="hidden" name="return" value="'.ui_form_safe($golf_config['paypay_return_url']).'">
											<input type="hidden" name="cancel_return" value="'.ui_form_safe($golf_config['paypay_return_url']).'">';
								}
			$member_paypal .= '
								<input type="hidden" name="no_note" value="1">
								<input type="hidden" name="cn" value="Description of payment">
								<input type="submit" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!" value="Tournament $'.sprintf('%.2f',$trow['tournament_cost_member']).'">
								</form>';
			
			$memberfee_paypal = '<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
								<input type="hidden" name="cmd" value="_xclick">
								<input type="hidden" name="business" value="'.ui_form_safe($golf_config['paypal_email']).'">
								<input type="hidden" name="currency_code" value="USD">
								<input type="hidden" name="amount" value="'.sprintf('%.2f',$golf_config['membership_fee']).'">
								<input type="hidden" name="item_name" value="DFWLGA membership fee: %s">';
								if (strlen($golf_config['paypay_return_url'])) {
									$s .= '<input type="hidden" name="return" value="'.ui_form_safe($golf_config['paypay_return_url']).'">
											<input type="hidden" name="cancel_return" value="'.ui_form_safe($golf_config['paypay_return_url']).'">';
								}
			$memberfee_paypal .= '
								<input type="hidden" name="no_note" value="1">
								<input type="hidden" name="cn" value="Description of payment">
								<input type="submit" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!" value="%s, $'.sprintf('%.2f',$golf_config['membership_fee']).'">
								</form>';
			
			$nonmember_paypal = '<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
								<input type="hidden" name="cmd" value="_xclick">
								<input type="hidden" name="business" value="'.ui_form_safe($golf_config['paypal_email']).'">
								<input type="hidden" name="currency_code" value="USD">
								<input type="hidden" name="amount" value="'.sprintf('%.2f',$trow['tournament_cost_nonmember']).'">
								<input type="hidden" name="item_name" value="DFWLGA tournament entry fee (nonmember): %s">';
								if (strlen($golf_config['paypay_return_url'])) {
									$s .= '<input type="hidden" name="return" value="'.ui_form_safe($golf_config['paypay_return_url']).'">
											<input type="hidden" name="cancel_return" value="'.ui_form_safe($golf_config['paypay_return_url']).'">';
								}
			$nonmember_paypal .= '
								<input type="hidden" name="no_note" value="1">
								<input type="hidden" name="cn" value="Description of payment">
								<input type="submit" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!" value="Tournament $'.sprintf('%.2f',$trow['tournament_cost_nonmember']).'">
								</form>';
			
			
			$tournament_date = $trow['tournament_date'];
			$tournament_deadline = $trow['tournament_deadline'];
			$tournament_flights = $trow['tournament_flights'];
			
			// retrieve teebox list
			$sql='select teebox_id, teebox_name from '. $user_prefix . '_golf_course_teeboxes where course_id='.$trow['course_id'].' order by teebox_slope DESC';
			$result = $db->sql_query($sql);
			if ($db->sql_numrows($result)) {
				while ($row = $db->sql_fetchrow($result)) {
					$teebox_list[$row['teebox_id']] = ui_form_safe($row['teebox_name']);
				}
			}
			//***********************************************************************************
			// ************************* show tournament info **************************************
			//***********************************************************************************
			$s .= 
			'<tr><td>
			
			<input type="hidden" name="tournament_id" value="'.$tournament_id.'"/>
			<table align="center" width="500">
				<tr><td align="right" valign="top">Tournament:</td><td>'.ui_form_safe($trow['tournament_name']).'</td></tr>
				<tr><td align="right" valign="top">Date:</td><td><b>'.date('D, M j, Y, g:ia',$tournament_date).'</b></td></tr>';
			if (!$_GET['grouping']) {
				$s .= '
				<tr><td align="right" valign="top"><b>Deadline:<b></td><td><b>'.date('D, M j, Y, g:ia',$tournament_deadline).'</b></td></tr>
				<tr><td align="right" valign="top">Maximum Players:</td><td>'.ui_form_safe($trow['tournament_max_players']).'</td></tr>
				<tr><td align="right" valign="top">Cost (member, golf, food, prizes):</td>
				<td nowrap>
					<table><tr><td><b>'.($trow['tournament_cost_member']>0?sprintf('$%.2f',$trow['tournament_cost_member']):'TBD').'</b>/pp</td>';
					if ($today < $tournament_date && strlen($golf_config['paypal_email']) && $trow['tournament_cost_member']>0 ) {
						$s .= '<td>Pay with Paypal, see your name below</td>';
					}
					$s .= '</tr></table>
				</td></tr>
				<tr><td align="right" valign="top">Membership/Year:</td>
				<td nowrap>
					<table><tr><td><b>$'.sprintf('%.2f', $golf_config['membership_fee']).'</b>/pp</td>';
					if ($today < $tournament_date && strlen($golf_config['paypal_email']) ) {
						$s .= '<td>Pay with Paypal, see your name below</td>';
						$s .= '<td>'.($trow['tournament_cost_nonmember']-$trow['tournament_cost_member']>0?'Join and save <b>$' . sprintf('%.2f', $trow['tournament_cost_nonmember']-$trow['tournament_cost_member']):'') . '</b></td>';
					}
					$s .= '</tr></table>
				</td></tr>
				<tr nowrap><td align="right" valign="top">Cost (non-member, golf only):</td>
				<td nowrap>
					<table><tr><td><b>'.($trow['tournament_cost_nonmember']>0?sprintf('$%.2f',$trow['tournament_cost_nonmember']):'TBD').'</b>/pp</td>';
					if ($today < $tournament_date && strlen($golf_config['paypal_email']) && $trow['tournament_cost_nonmember'] > 0 ) {
						$s .= '<td>Pay with Paypal, see your name below</td>';
					}
					$s .= '</tr></table>
				</td></tr>';
				if ( strlen($golf_config['mail_address']) ) {
					$s .= '<tr><td align="right" valign="top">Payments can be mail to:</td><td>'.nl2br($golf_config['mail_address']).'</td></tr>';
				}
			}
				$sql = 'select * from '.$user_prefix.'_golf_tournament_flights where tournament_id='.$trow['tournament_id'].' order by flight_max_hcp';
				$result = $db->sql_query($sql);
				$flights_count = $db->sql_numrows($result);
				
				$s .= '
				<input type="hidden" name="course_url" value="'.$trow['course_url'].'"/>
				<tr><td align="right" valign="top">Course:</td><td>'.(strlen($trow['course_url'])?'<a href="'.ui_form_safe($trow['course_url']).'">'.ui_form_safe($trow['course_name']).'</a>':ui_form_safe($trow['course_name'])).' '.ui_form_safe($trow['course_phone']).' <a href="http://www.google.com/maps?f=q&hl=en&q='.urlencode($trow['course_address'] . ', ' . $trow['course_city'] . ', ' . $trow['course_state']).'" target="_blank">Map</a></td></tr>
				<tr><td align="right" valign="top">Course Tee Box:</td><td>'.($flights_count?'See flight details for tee box':$trow['teebox_name']).'</td></tr>
				<tr><td align="right" valign="top"><b>Format:</b></td><td>'.$trow['tournament_format'].'</td></tr>
				<tr><td align="right" valign="top">Flights:</td><td>
					<table border="solid 1px" cellpadding="0" cellspacing="0">';
				if ($flights_count) {
					$n_row = '<tr><td align="right">Flight:</td>';
					$h_row = '<tr><td align="right">Max HCP:</td>';
					$t_row = '<tr><td align="right">Teebox:</td>';
					$f = 0;
					while ($row = $db->sql_fetchrow($result)) {
						if ($row['flight_max_hcp'] == 0) $row['flight_max_hcp'] = 36.4;
						$flight_hcp[$row['flight_id']] = $row['flight_max_hcp'];
						$flight_list[$row['flight_id']] = ui_form_safe($row['flight_name']); 
						$f++;
						$n_row .= '<td nowrap>'.ui_form_safe($row['flight_name']).'</td>';
						$h_row .= '<td nowrap>'.ui_form_safe($row['flight_max_hcp']).'</td>';
						$t_row .= '<td nowrap>'.ui_form_safe($teebox_list[$row['teebox_id']]).'</td>';
					}
					$n_row .= '</tr>';
					$h_row .= '</tr>';
					$t_row .= '</tr>';
					$s .= $n_row.$h_row.$t_row;
				} else {
					$s .= '<tr><td>None</td></tr>';
				}
				
				$side_contest_count = 0;
				$sql = 'select gtsc.*, u.username, gts.signup_name, concat(\'#\',hole_number, \' - Par \', hole_par) hole_desc,
				flight_name from '.$user_prefix.'_golf_tournament_side_contests gtsc 
				left join '.$user_prefix.'_golf_tournament_signups gts on gts.signup_id=gtsc.winner_signup_id 
				left join '.$user_prefix.'_users u on u.user_id=gts.user_id 
				left join '.$user_prefix.'_golf_holes gh on gh.hole_id=gtsc.hole_id
				left join '.$user_prefix.'_golf_tournament_flights gtf on gtf.flight_id=gtsc.flight_id
				where gtsc.tournament_id='.$trow['tournament_id'].' order by gtsc.hole_id, gtsc.contest_id';
				$result = $db->sql_query($sql);
				$side_contest_count = $db->sql_numrows($result);
				//echo '<pre>'.$sql.'</pre>';
				$s .= '</table></td></tr>
				<tr><td align="right" valign="top">Side Contests:</td>
					<td>
					<table cellpadding="0" cellspacing="0" border="solid 1px">';
					if ($side_contest_count) {
						
						$h_row = '<tr><td align="right">Hole:</td>';
						$c_row = '<tr><td align="right">Contest:</td>';
						$f_row = '<tr><td align="right">Flight:</td>';
						$w_row = '<tr><td align="right">Winner:</td>';
						$n_row = '<tr><td align="right">Note:</td>';
						$h = 0;
						while ($row = $db->sql_fetchrow($result)) {
							$h++;
							$h_row .= '<td nowrap align="center">'.ui_form_safe($row['hole_desc']).'</td>';
							$c_row .= '<td nowrap align="center">'.ui_form_safe($hole_contests[$row['contest_type']]).'</td>';
							$f_row .= '<td nowrap align="center">'.ui_form_safe($row['flight_name']?$row['flight_name']:'all').'</td>';
							$w_row .= '<td nowrap align="center">'.ui_form_safe($row['username']?$row['username']:$row['signup_name']).'&nbsp;</td>';
							$n_row .= '<td nowrap align="center">'.ui_form_safe($row['contest_note']).'&nbsp;</td>';
						}
						$h_row .= '</tr>';
						$c_row .= '</tr>';
						$f_row .= '</tr>';
						$w_row .= '</tr>';
						$n_row .= '</tr>';
						$s .= $h_row.$c_row.$f_row.$w_row.$n_row;
					} else {
						$s .= '<tr><td>None</td></tr>';
					}
				$s .= '</table></td></tr>';
				if ($trow['tournament_partner_require']) {
					//TODO: partner require logic
					//<tr><td align="right"># of Partners</td><td>'.?$trow['tournament_partner_require']:'').'" size="5" maxlength="5"/></td></tr>
				}
				$s .= '
				<tr><td align="right" valign="top">Mulligan:</td><td>'.ui_form_safe($trow['tournament_mulligan']).'</td></tr>
				<tr><td align="right" valign="top">Prizes:</td><td>'.ui_form_safe($trow['tournament_prizes']);
				if (!$trow['tournament_nonmember_winner']) {
					$s .= '<br><b>Only members can win prizes.</b>';
				}
				$s .= '</td></tr>';
				$s .= '
				<tr><td align="right" valign="top">Note:</td><td>'.ui_form_safe($trow['tournament_note']).'</td></tr>';
				if (strlen($trow['tournament_results'])) {
					$s .= '<tr><td align="right" valign="top">Results:</td><td>'.nl2br($trow['tournament_results']).'</td></tr>';					
				}
			$s .= '
			</table>
			</td></tr>';
			$html .= $s; $s = '';
			
			$g = '<tr><td colspan="2"><table border=1 cellspacing=0 cellpadding=2>';
			$g .= '<tr><td colspan="3" align="center"><b>Grouping</b></td></tr>';
			$g .= '<tr><td width="20%">Group #</td><td width="80%">Name</td><td></td></tr>';
			
			$s .= '<tr><td><table align="center"><tr><td><b>Sign Up List</b></td></tr></table></td></tr>';
			$s .= '<tr><td>';
			$s .= '<table border="1px solid black" cellpadding=1 cellspacing=0 align="center">';
			$s .= '<tr><td colspan="2"><b>Name</b></td><td><b>Handicap</b></td><td><b>Flight</b></td>';
			//if (time() < $tournament_date) {
				$s .= '<td><b>Group</b></td><td><b>Paypal</b></td>';
				//<td>Member</td>
			//}
			$s .= '<td colspan="2">Gross</td><td colspan="2">Net</td><td>Stats</td>'.
				($admin?'<td>sign up on</td>':'').
				($admin&&!$_GET['no_header']?'<td>edit</td>':'').
				($admin&&!$_GET['no_header']?'<td>delete</td>':'').
				'</tr>';
			$signup_total=0; $flight_totals = array();
			$sql = 'select u.username, u.user_email, gts.*, IF(ISNULL(gp.player_handicap_temp),99,gp.player_handicap_temp) player_handicap_temp, 
				gr.round_score_temp, gr.round_id, gr2.round_score_temp round_score_temp2, 
				gr2.round_id round_id2, UNIX_TIMESTAMP(signup_time) as signup_time_unix, flight_net_awards, flight_gross_awards,
				player_handicap_temp, gtf.teebox_id, UNIX_TIMESTAMP(player_member_renew_date) player_member_renew_date_unix,
				UNIX_TIMESTAMP(player_member_date) player_member_date_unix '.
				' from '. $user_prefix.'_golf_tournament_signups gts 
				left join '.$user_prefix.'_users u on u.user_id=gts.user_id 
				left join '.$user_prefix.'_golf_rounds gr on (gr.user_id=gts.user_id and gr.tournament_id=gts.tournament_id and gr.user_id > 0)
				left join '.$user_prefix.'_golf_rounds gr2 on (gr2.signup_id=gts.signup_id and gr2.tournament_id=gts.tournament_id and gr2.signup_id > 0)
				left join '.$user_prefix.'_golf_tournament_flights gtf on gtf.flight_id=gts.flight_id
				left join '.$user_prefix.'_golf_players gp on gp.user_id=gts.user_id
				where gts.tournament_id='.$tournament_id.' group by gts.signup_id';
				if (time() > $tournament_date) {
					// order by flight and score after tournament date
					$sql .= ' order by signup_withdraw asc, flight_max_hcp, signup_member desc, signup_gross_standing, round_score_temp2, round_score_temp ';
				} else {
					// order by group before tournament
					$sql .= ' order by signup_withdraw asc, signup_group, player_handicap_temp, signup_handicap';
				}
				//echo '<pre>'.$sql.'</pre>';				
			$result = $db->sql_query($sql);
			if ($db->sql_numrows($result)) {
				//***********************************************************************************
				// **************** show all signups ********************************************
				//***********************************************************************************
				$separate_flights = array(); 
				$flight_format = array(); $flight_count = 0;
				$pairings = '';
				$group_number = 0;
				$emails = array();
				while ($row = $db->sql_fetchrow($result)) {
					$flight_name = $flight_list[$row['flight_id']];
					if (strlen($flight_name) == 0) {
						foreach ($flight_hcp as $id => $max_hcp) {
							if ( $row['signup_handicap'] <= $max_hcp ) {
								$flight_name = $flight_list[$id];
								$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET flight_id='.$id.' WHERE signup_id='.$row['signup_id'];
								$db->sql_query($sql);								
								break;
							}
						}
					}
					if (!isset($flight_format[$flight_name])) {
						$flight_count++;
						switch ($flight_count) {
							case 1:
							$flight_format[$flight_name] = array(0=>'<b>',1=>'</b>');break;
							case 2:
							$flight_format[$flight_name] = array(0=>'<i>',1=>'</i>');break;
							case 3:
							$flight_format[$flight_name] = array(0=>'<u>',1=>'</u>');break;
							default:
							$flight_format[$flight_name] = array(0=>'',1=>'');break;
						}
					}
					if (time() > $tournament_date) {
						if (!isset($separate_flights[$flight_name])) {
							$separate_flights[$flight_name] = '';
							$s .= '<tr><td colspan="20">&nbsp;</td></tr>';
						}
					} else {
						if ($row['player_handicap_temp'] != $row['signup_handicap'] && strlen($row['player_handicap_temp'])) {
							$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET signup_handicap="'.$row['player_handicap_temp'].'" WHERE signup_id="'.$row['signup_id'].'" LIMIT 1';
							$db->sql_query($sql);
						}
					}
					
					$s .= '<tr><td>';
					
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
						<a href="'.$player_photo_file_full.'" rel="gb_imageset[signup]" title="'.$row['signup_name'].'">
					  	<img height="20" src="'.$player_photo_file.'" border=0/>
						</a';
					}

					
					$s .= '</td><td>'.($row['signup_withdraw']?'<s>':'').
					($admin?'<a href="mailto:'.ui_form_safe($row['signup_email']?$row['signup_email']:$row['user_email']).'">':'').(strlen($row['username'])?ui_form_safe($row['username']):$row['signup_name']).
					($admin?'</a>':'').'&nbsp;'.($row['signup_withdraw']?'</s>':'').'</td><td>';
					if ($row['user_id']) {
						$s .= '<a href="/modules.php?name=Golf&op=players_scores&user_id='.$row['user_id'].'">';
					}
					//$s .= sprintf('%.1f',$row['player_handicap_temp']>0?$row['player_handicap_temp']:$row['signup_handicap']).'&nbsp;';
					$s .= sprintf('%.1f',$row['signup_handicap']).'&nbsp;';
					if ($row['user_id']) {
						$s .= '</a>';
					}
					
					$fee = ($row['signup_member'])?$trow['tournament_cost_member']:$trow['tournament_cost_nonmember'];
					
					$s .= '<td>'.$flight_format[$flight_name][0].$flight_name.$flight_format[$flight_name][1].'&nbsp;</td>';
					
					//if (time() < $tournament_date) {
						$renewal_date = 0;
						if ($row['signup_member']) {
							if ($row['player_member_renew_date_unix']) {
								$renewal_date = $row['player_member_renew_date_unix'];
							} else if ($row['player_member_date_unix']) {
								$renewal_date = $row['player_member_date_unix'];
							}
						}
						$last_year = mktime(0,0,0,date('m'),date('d'),date('Y')-1);
						$s .= '<td>'.($row['signup_group']?$row['signup_group']:'&nbsp;').'</td>';

						// 2003-02-27 gee - not using paypal anymore as decided by officers
						$s .= '<td>';
						if ($row['signup_paid'] >= $fee) {
							$s .= 'PAID';
						} else if ($row['signup_member']) {
							//$s .= sprintf($member_paypal, $row['signup_name']);
						} else {
							//$s .= sprintf($nonmember_paypal, $row['signup_name']);
						}
						if ($row['signup_member']) {
							if ($renewal_date<$last_year) {
								//$s .= sprintf($memberfee_paypal, $row['signup_name'], 'Membership');
							}
							if ($admin) {
								if ($renewal_date) {
									$s .= date('m-d-y',$renewal_date);
								} else {
									$s .= '???';
								}
							}
							//$s .= '</b>';
						} else {
							//$s .= sprintf($memberfee_paypal, $row['signup_name'], 'Membership');
						}
						$s .= '</td>';						
							
					//}
					
					$s .= '</td>';

					if (time() > $tournament_date) {
						$net_round_score = 0;
						if ($row['round_score_temp']) {
							$round_score = $row['round_score_temp'];
							$round_id = $row['round_id'];
						} else {
							$round_score = $row['round_score_temp2'];
							$round_id = $row['round_id2'];
						}
						if ($row['flight_net_awards']) {
							if (!$row['signup_withdraw']) {
								if (intval($round_score) > 0) {
									$net_round_score = $round_score-$row['signup_handicap'];
								}
							}
						}
						$s .= '<td><i>'.($row['signup_gross_standing']?$row['signup_gross_standing']:'&nbsp;').'</i></td><td style="border-left:none;">';
						if ($admin) {
							if (!$row['signup_withdraw']) {
								$s .= '<a href="/modules.php?name=Golf&op=players_scores&user_id='.$row['user_id'].'&signup_id='.$row['signup_id'].'&tournament_id='.$trow['tournament_id'].'&round_id='.$round_id.'&teebox_id='.$row['teebox_id'].'">'.($round_score?'<b>'.$round_score.'</b>':'enter score').'</a>';
							} else {
								$s .= '&nbsp;';
							}
						} else {
							if (!$row['signup_withdraw']) {
								$s .=  $round_score?'<b>'.$round_score.'</b>&nbsp;':'coming soon';
							} else {
								$s .= '&nbsp;';
							}
						}
						$s .= '</td>';
						$s .= '<td><i>'.($row['signup_net_standing']?$row['signup_net_standing']:'&nbsp;').'</i></td><td style="border-left:none; font-weight:bold;">'.($net_round_score?sprintf('%.1f',$net_round_score):'&nbsp;').'</td>';
					} else {
						$s .= '<td>&nbsp;</td><td style="border-left:none;">&nbsp;</td><td>&nbsp;</td><td style="border-left:none;">&nbsp;</td>';
					}
					$s .= '<td>';
					if (!$_GET['no_header']) {
						$s .= ($row['user_id']?'<a href="/modules.php?name=Golf&op=players_stats&user_id='.$row['user_id'].'">stats</a>':'&nbsp;');
					}
					$s .= '</td>'.
					($admin?'<td>'.date('M, j g:ia', $row['signup_time_unix']).'</td>':'').
					($admin&&!$_GET['no_header']?'<td><a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$tournament_id.'&signup_id='.$row['signup_id'].'&tournament_year='.$_GET['tournament_year'].'">edit</a></td>':'').
					($admin&&!$_GET['no_header']?'<td><a href="/modules.php?name=Golf&op=tournaments_signup_delete&tournament_id='.$tournament_id.'&signup_id='.$row['signup_id'].'&tournament_year='.$_GET['tournament_year'].'">delete</a></td>':'').
					'</tr>';
					if ($row['signup_id'] == $signup_id) {
						$edit_row = $row;
					}
					if (!$row['signup_withdraw']) {
						$active_count++;
						if (!isset($flight_totals[$flight_name])) {
							$flight_totals[$flight_name] = 0;
						}
						$flight_totals[$flight_name]++;
						$signup_total++;
						$emails[] = $row['signup_email']?$row['signup_email']:$row['user_email'];
						if ($group_number != $row['signup_group']) {
							if ($group_number) {
								$group_number = $row['signup_group'];
								$pairings .= '<br><u>Group '.$group_number.'</u>:'."\t";
							}
						}
						if ($group_number) {
							$pairings .= ($row['username']?$row['username']:$row['signup_name'])."&nbsp;&nbsp;&nbsp;";
						}
						
						$g .= '<tr>';
						$g .= '<td><b>'.($row['signup_group']?$row['signup_group']:sprintf('%d',(ceil($active_count/4)))).'</b></td>';
						$g .= '<td>'.($row['signup_group']?(strlen($row['username'])?ui_form_safe($row['username']):$row['signup_name']).($flight_name?'flight '.$flight_name:''):'&nbsp;').'</td>';
						$g .= '</tr>';
						
					}
				}
				$emails = array_unique($emails);
				if ($admin) {
					$s .= '<tr><td colspan="6"><s>player name</s> = withdrawn/cancelled</td><td colspan="5">
						<a href="mailto:'.implode(';',$emails).'">all signup emails</a>
						&nbsp;&nbsp;<a href="/modules.php?name=Golf&op=tournaments_signups_cal_standing&tournament_id='.$tournament_id.'&tournament_year='.$_GET['tournament_year'].'">calculate standing</a>
						<br>'.$pairings.'</td></tr>';
				}
			}
			$s .= '</td></tr></table>';
			$s .= '</td></tr>';
			$s .= '<tr><td><table align="center"><tr><td>Total:'.$signup_total.'<br>';
			foreach ($flight_totals as $k => $v) {
				$s .= '&nbsp;Flight '.$k.':'.$v;
			}
			$s .= '</td></tr></table></td></tr>';
			if ($admin) {
				$s .= '<tr><td align="center">Handicap will pull from current handicap up to the tournament start time.</td></tr>';
			}
			
			$s .= '<tr><td><table align="center"><tr><td></td></tr></table></td></tr>';
			//***********************************************************************************
			// ***************** add/edit sign up ***********************************************
			//***********************************************************************************
			if ( ($today > $tournament_deadline || $signup_total >= $trow['tournament_max_players']) && !$admin) {
				$s .= '<tr><td><table align="center"><tr><td><b>Online sign up is closed for this tournament.';
				if ($today < $tournament_date && !$admin) {
					$s .= ' Please contact dfwlga to be put on the waiting list.';
				}
				$s .= '</b></td></tr></table></td></tr>';
			} else if (!$admin && $today < $tournament_deadline) {
				$s .= '<tr><td><table align="center"><tr><td>';
				$s .= 'To sign up, provide your name, email and phone # <a href="?name=Feedback&signup=1&p='.($trow['tournament_partner_require']+1).'&msg='.htmlentities(urlencode($trow['tournament_name'])).'&no_header=1" rel="gb_page[600,600]">HERE</a>';
				$s .= '</b></td></tr></table></td></tr>';
				
			} else {
				if (!$_GET['no_header']) {
					$s .= '<tr><td><table align="center"><tr><td><b>'.(isset($edit_row)?'Edit Sign Up':'Sign Me Up!').'</b></td></tr></table></td></tr>';
					$s .= 
						'<tr><td>
						<form name="tournaments_signup" action="/modules.php?name=Golf&op=tournaments_signup_addedit&tournament_year='.$_GET['tournament_year'].'" method="POST"/>
						<input type="hidden" name="signup_id" value="'.$signup_id.'"/>
						<input type="hidden" name="tournament_id" value="'.$tournament_id.'"/>
						<input type="hidden" name="signup_paid" value="'.$edit_row['signup_paid'].'"/>
						<table align="center" width="100px">
						<script language="javascript">
						var handicaps = new Array();
						var members = new Array();';
					foreach ($players as $k => $v) {
						$s .= 'handicaps['.$k.']='.$players_handicap[$k].';';
						$s .= 'members['.$k.']='.$players_member[$k].';';
					}
					$s .= '
						function getObj(name)
						{
						  if (document.getElementById) { return document.getElementById(name); }
						  else if (document.all) { return document.all[name]; }
						  else if (document.layers) { return document.layers[name]; }
						}
						function user_selected( ) {
							var f = document.tournaments_signup;
							f.signup_name.value=f.user_id.options[f.user_id.selectedIndex].text;
							//obj = getObj("extra_info");
							//obj.style.visibility = "hidden";
							f.signup_handicap.value = handicaps[f.user_id.options[f.user_id.selectedIndex].value];
							f.signup_member.value = 0; // value for signup by player
							f.signup_member.checked = 0; // checked for signup by admin
							if (members[f.user_id.options[f.user_id.selectedIndex].value]) {
								f.signup_member.value = 1;
								f.signup_member.checked = 1;
							}
						}
						</script>
						';
							$s .= '
							<tr>
							<td valign="top"><table>
								<tr><td align="right" valign="top">'.($trow['tournament_partner_require']>0?'Team ':'').'Name:</td><td valign="top"><input type="text" name="signup_name" value="'.(isset($edit_row)?$edit_row['signup_name']:'').'" size="32" maxlength="32"/><td></tr>
							';
							if ($trow['tournament_partner_require']>0) {
								$s .= '
								<tr><td valign="top" colspan="2">Enter your team members, ex: kham,boun</td></tr>
								';
							} else {
								$s .= '
								<tr><td valign="top" colspan="2">Or select from the list below if you have <a href="/modules.php?name=Your_Account&op=new_user">registered</a> with us, this way we can show your stats and track your scores/handicap on the <a href="/modules.php?name=Golf&op=players">Players/Stats</a> page.</td></tr>
								<tr><td></td><td>'.ui_select_box($players, 'user_id', (isset($edit_row)?$edit_row['user_id']:''), 'size=20 width=30 onchange="user_selected()"').'</td></tr>';
							}
							$s .= '
							</table></td>
							<td valign="top">
								<div id=extra_info>
								<table>
								<tr><td align="right" valign="top">'.($trow['tournament_partner_require']>0?'Team ':'').'Handicap:</td><td><input type="text" name="signup_handicap" value="'.(isset($edit_row)?sprintf('%.1f',$edit_row['signup_handicap']):'').'" size="6" maxlength="10" '.($trow['tournament_partner_require']?'readonly':'').'/>';
							if ($trow['tournament_partner_require']) {								
								$s .= 'Team HCP will be calculated for you, enter each player HCP below.<br>HCP1<input type="text" name="signup_handicap1" value="" size="6" maxlength="10"/><br>HCP2<input type="text" name="signup_handicap2" value="" size="6" maxlength="10"/>';
							}
							$s .= '(36.4 maximum, don\'t have a handicap? click <a href="/modules.php?name=Golf&op=players">here</a></td></tr>';
								if ($admin) {
									$s .= '<tr><td align="right">Flight:</td><td>'.ui_select_box($flight_list, 'flight_id', isset($edit_row)?$edit_row['flight_id']:0).'</td></tr>';
								}
								$s .= '
								<tr><td align="right">Email:</td><td><input type="text" name="signup_email" value="'.(isset($edit_row)?ui_form_safe($edit_row['signup_email']):'').'" size="32" maxlength="64"/></td></tr>
								<tr><td align="right">Phone:</td><td><input type="text" name="signup_phone" value="'.(isset($edit_row)?ui_form_safe($edit_row['signup_phone']):'').'" size="20" maxlength="32"/></td></tr>';
								if ($admin) {
									$s .= '<tr><td align="right">Member:</td><td><input type="checkbox" name="signup_member" value="1" '.(isset($edit_row)?(($edit_row['signup_member'] || $edit_row['player_member'])?'checked':''):'').'/></td></tr>
									<tr><td align="right">Paid:</td><td><input type="text" name="signup_paid" value="'.(isset($edit_row)?(sprintf('%.2f',$edit_row['signup_paid'])):'').'"/></td></tr>
									<tr><td align="right">Group:</td><td><input type="text" name="signup_group" value="'.(isset($edit_row)?$edit_row['signup_group']:'').'" size="10" maxlength="10"/></td></tr>
									<tr><td align="right">Withdraw</td><td><input type="checkbox" name="signup_withdraw" value="1" '.(isset($edit_row)?($edit_row['signup_withdraw']?'checked':''):'').'/></td></tr>';
									$places = range(0, $signup_total);
									if ($edit_row['flight_gross_awards']) {
										$s .= '<tr><td align="right">Standing (Gross):</td><td>'.ui_select_box($places, "signup_gross_standing", $edit_row['signup_gross_standing']).'</td></tr>';
									}
									if ($edit_row['flight_net_awards']) {
										$s .= '<tr><td align="right">Standing (Net):</td><td>'.ui_select_box($places, "signup_net_standing", $edit_row['signup_net_standing']).'</td></tr>';
									}
								}else {
									$s .= '<input type="hidden" name="signup_member" value="0"/>';
								}
								$s .= '<tr><td align="right" valign="top">Note:</td><td><font size="1">
								By signing up, you are hereby agreed 
								to submit payment to dfwlga in the amount 
								indicated at least one hour prior to the 
								start of the tournament. Thanks for your cooperation. 
								Any cancellation must be made by the tournament deadline.</font>
								</td></tr>
								</table>
								</div>
							<tr><td align="right"></td><td><input type="submit" value="'.(isset($edit_row)?'update':'sign up').'"/></td></tr>
							<tr><td>
						</table>
						</form>
						</td></tr>';
				}
			}
			
		}
	}
	if ($_GET['grouping']) {
		$html .= $g.'</table></td></tr>';
	} else {
		$html .= $s;
	}
	$html .= '</table>';
	echo $html;
	
}
function tournaments_signup_addedit() {
	global $user, $admin, $db, $user_prefix, $user_info, $sitename, $nukeurl, $golf_config;
	$signup_id = $_POST['signup_id'];
	if( $signup_id && !$admin ) {
		echo '<span class="error">Registered user only, please register and login.</span>';
		return;  
	}
	$tournament_id = $_POST['tournament_id'];
	
	$user_id = (isset($_POST['user_id'])?$_POST['user_id']:0);
	$signup_name = $_POST['signup_name'];
	$signup_handicap = $_POST['signup_handicap'];
	$signup_email = $_POST['signup_email'];
	$signup_phone = $_POST['signup_phone'];
	$check_msg = '';
	if (strlen($signup_name)==0 && $user_id == 0) {
		if (!$admin) {
			$check_msg .= 'you forgot to enter a name, ';
		}
	}
	if (intval($signup_handicap) > 36.4) { // && $user_id == 0) {
		if (!$admin) {
			$check_msg .= 'handicap must be 36.4 or less, ';
		}
	}
	if (strlen($signup_email)==0) { // && $user_id == 0) {
		if (!$admin) {
			$check_msg .= 'you forgot to enter an email, ';
		}
	}
	if (strlen($signup_phone)==0 ) { //&& $user_id == 0) {
		if (!$admin) {
			$check_msg .= 'you forgot to enter a phone #';
		}
	}
	
	$hcp1 = $_POST['signup_handicap1'];
	$hcp2 = $_POST['signup_handicap2'];
	if (isset($_POST['signup_handicap2'])) {
		if (strlen($hcp1) > 0 && strlen($hcp2) > 0) {
			$_POST['signup_handicap'] = $hcp1 + $hcp2;
			/*
			if ($hcp1 == '') $hcp1 = 0.0;
			if ($hcp2 == '') $hcp2 = 0.0;
			$thcp = 0;
			if ($hcp1 > $hcp2 && $hcp1 > 0) {
				$thcp = $hcp2 - (($hcp2*($hcp2/40)*5)/$hcp1);
			} else if ($hcp2 > 0) {
				$thcp = $hcp1 - (($hcp1*($hcp1/40)*5.0)/$hcp2);
			}
			$_POST['signup_handicap'] = $thcp;
		} else {
			if ($hcp1 > 0 || $hcp2 > 0) {
				$_POST['signup_handicap'] = $hcp1 + $hcp2;
			}
		*/
		}
	}
		
		
	
	if (strlen($check_msg)==0) {
		if ($signup_id) {
			$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET 
				signup_name="'.addslashes($_POST['signup_name']).'",
				signup_handicap="'.FixQuotes($_POST['signup_handicap']).'",
				signup_email="'.FixQuotes($_POST['signup_email']).'",
				signup_phone="'.strtoupper(FixQuotes($_POST['signup_phone'])).'",';
				if ($admin) {
					$sql .= '
					flight_id="'.strtoupper(FixQuotes($_POST['flight_id'])).'",
					signup_member="'.FixQuotes($_POST['signup_member']).'",
					signup_paid="'.FixQuotes($_POST['signup_paid']).'",
					signup_group="'.FixQuotes($_POST['signup_group']).'",
					signup_withdraw="'.FixQuotes($_POST['signup_withdraw']).'",
					signup_gross_standing="'.FixQuotes($_POST['signup_gross_standing']).'",
					signup_net_standing="'.FixQuotes($_POST['signup_net_standing']).'",
					';
				}
				$sql .= '
				tournament_id="'.FixQuotes($_POST['tournament_id']).'",
				user_id="'.FixQuotes($_POST['user_id']).'"
				WHERE signup_id='. $signup_id;
		} else {
			$sql = 'INSERT INTO '.$user_prefix.'_golf_tournament_signups (signup_name, signup_handicap, flight_id, signup_email,
				signup_phone, signup_time, signup_member, signup_group, tournament_id, user_id)
				VALUES ("'.addslashes($_POST['signup_name']).'",
				"'.FixQuotes($_POST['signup_handicap']).'","'.FixQuotes($_POST['flight_id']).'","'.FixQuotes($_POST['signup_email']).'","'.FixQuotes($_POST['signup_phone']).'",
				"'.date('y-m-d H:i').'","'.FixQuotes($_POST['signup_member']).'","'.FixQuotes($_POST['signup_group']).'",
				"'.FixQuotes($_POST['tournament_id']).'","'.FixQuotes($_POST['user_id']).'")';
		}
	
		$result = $db->sql_query($sql);
		if(!$result) {
			echo ""._ERROR." $sql<br>";
			exit();
		}
		if (!$signup_id) {	 // new sign up
			$result = $db->sql_query('SELECT tournament_name, UNIX_TIMESTAMP(tournament_date) tournament_date FROM '.$user_prefix.'_golf_tournaments WHERE tournament_id='.$tournament_id);
			$tournament = $db->sql_fetchrow($result);
			$signup_email = $_POST['signup_email'];
			if ($_POST['user_id'] && strlen($signup_email) == 0) {
				$result = $db->sql_query('SELECT user_email FROM '.$user_prefix.'_users WHERE user_id='.$_POST['user_id']);
				$row = $db->sql_fetchrow($result);
				$signup_email = $row['user_email'];
			}
			$signup_name = ucwords($_POST['signup_name']);
			$email_msg = "$signup_name,<br><br>
			Thank you for signing up for the <b>".$tournament['tournament_name'] . '</b> hosted by <b>' . $sitename . '</b> 
				on <b>' . date('D, M d, Y h:ia',$tournament['tournament_date']).'</b>.<br><br>';
			if (strlen($_POST['course_url'])) {
				$email_msg .= 'For golf course information visit their website at <a href="'.$_POST['course_url'].'">'.$_POST['course_url'].'</a><br><br>';
			}
			$email_msg .= '
				<span style="font-size: 9">
					By signing up, you are hereby agreed to submit payment to dfwlga for the amount indicated at least one hour prior to the start of the tournament. 
					Cancellation must be made before the tournament sign up deadline. If you signed up by mistake, please contact us. Thank you for your cooperation.
				</span>
				<br><br>
				<center><i><b>Have fun and good luck!</b></i><br><br>'.
					$golf_config['tournament_rules'].'				
				</center>';
			$headers = 'From: '.$sitename.' <'.$golf_config['tournament_email'].">\r\nCc: <".$golf_config['tournament_email'].">";
			if ($tournament['tournament_date'] > time) {
				html_mailer("$signup_name <$signup_email>", 'DFWLGA tournament sign up', $email_msg, $headers, 'Tournament Committee');
			}
		}
	} else {
		echo '<center>'.$check_msg.' <a href="javascript:window.history.back();">try again</a></center>';
		exit();
	}
	header('Location: modules.php?name=Golf&op=tournaments_signup&tournament_id='.$tournament_id.'&tournament_year='.$_GET['tournament_year']);
}
function tournaments_signup_delete() {
	global $admin, $db, $user_prefix;
	if(!$admin) {
		echo '<span class="error">Admin only!</span>';
		return; 
	}
	$signup_id = $_GET['signup_id'];
	$tournament_id = $_GET['tournament_id'];
	if ($signup_id) {
		$sql = 'DELETE FROM '.$user_prefix.'_golf_tournament_signups WHERE signup_id='.$signup_id.' LIMIT 1';
		$result = $db->sql_query($sql);
		if(!$result) {
			if ($admin) {
				echo ""._ERROR."<br>";
				exit();
			}
		}
	}
	header('Location: modules.php?name=Golf&op=tournaments_signup&tournament_id='.$tournament_id.'&tournament_year='.$_GET['tournament_year']);
}

function tournaments_signups_cal_standing() {
	global $admin, $db, $user_prefix;
	if(!$admin) {
		echo '<span class="error">Admin only!</span>';
		return; 
	}
	$tournament_id = $_GET['tournament_id'];

	$flight_net_scores = array();
	$flight_gross_standing = array();
	if ($tournament_id) {
		$sql = 'select gts.signup_id, gts.flight_id, gts.signup_handicap, gr.round_score_temp, gr2.round_score_temp round_score_temp2, 
		flight_net_awards, flight_gross_awards, signup_withdraw, player_member, tournament_nonmember_winner from '.
		$user_prefix.'_golf_tournament_signups gts 
		left join '.$user_prefix.'_users u on u.user_id=gts.user_id 
		left join '.$user_prefix.'_golf_rounds gr on (gr.user_id=gts.user_id and gr.tournament_id=gts.tournament_id and gr.user_id > 0)
		left join '.$user_prefix.'_golf_rounds gr2 on (gr2.signup_id=gts.signup_id and gr2.tournament_id=gts.tournament_id and gr2.signup_id > 0)
		left join '.$user_prefix.'_golf_tournament_flights gtf on gtf.flight_id=gts.flight_id
		left join '.$user_prefix.'_golf_tournaments gt on gt.tournament_id=gts.tournament_id
		left join '.$user_prefix.'_golf_players gp on gp.user_id=gts.user_id
		where gts.tournament_id='.$tournament_id.' group by gts.signup_id';
		$sql .= ' order by signup_withdraw asc, flight_max_hcp, round_score_temp2, round_score_temp';
		
		//echo '<pre>'.$sql.'</pre>';
		$result = $db->sql_query($sql);
		if ($db->sql_numrows($result)) {
			while ($row = $db->sql_fetchrow($result)) {
				if (!$row['signup_withdraw']) {
					if ($row['tournament_nonmember_winner'] || $row['player_member']) {
						if ($row['flight_gross_awards']) {
							if (!isset($flight_gross_standing[$row['flight_id']])) {
								$flight_gross_standing[$row['flight_id']] = 0;
							}
							if ($row['round_score_temp2'] > 0 || $row['round_score_temp']) { 
								$flight_gross_standing[$row['flight_id']]++;
								$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET signup_gross_standing='.$flight_gross_standing[$row['flight_id']].' WHERE signup_id='.$row['signup_id'];
								//echo '<pre>'.$sql.'</pre>';
							} else {
								$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET signup_gross_standing=999 WHERE signup_id='.$row['signup_id'];
							}
							$db->sql_query($sql);
						}
						if ($row['flight_net_awards']) {
							if (!isset($net_scores[$row['flight_id']])) {
								$net_scores[$row['flight_id']] = array();
							}
							$net_scores[$row['flight_id']][$row['signup_id']] = ($row['round_score_temp']?$row['round_score_temp']:$row['round_score_temp2']) - $row['signup_handicap'];
						}
					}
				} else {
					$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET signup_gross_standing=0, signup_net_standing=0 WHERE signup_id='.$row['signup_id'];
					$db->sql_query($sql);
				}
			}
			
			if (count($net_scores)) {
				foreach ($net_scores as $scores) {
					asort($scores);
					$count = 0;
					foreach ($scores as $signup_id => $v) {
						if ($v > 0) {
							$count++;
							$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET signup_net_standing='.$count.' WHERE signup_id='.$signup_id;
						} else {
							$sql = 'UPDATE '.$user_prefix.'_golf_tournament_signups SET signup_net_standing=999 WHERE signup_id='.$signup_id;
						}
						$db->sql_query($sql);
					}
				}
			}
		}
	}
	
	header('Location: modules.php?name=Golf&op=tournaments_signup&tournament_id='.$tournament_id.'&tournament_year='.$_GET['tournament_year']);	
}
function tournaments_rules( ) {
	global $golf_config;
	$s = '<table align="center">';
	
	$s .= '<tr><td><table align="center"><tr><td><b>Tournaments Rules</b></td></tr></table></td></tr>';
	$s .= '<tr><td><table align="center"><tr><td>'.$golf_config['tournament_rules'].'</td></tr></table></td></tr>';
	$s .= '</table>';
	echo $s;
	
}
function tournaments_tee_sheet() {
	
}
?>
