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
/* 2005.09.08  Created                                                  */
/************************************************************************/
if (!eregi("modules.php", $_SERVER['PHP_SELF'])) {
	die ("You can't access this file directly...");
}
include_once('./modules/Golf/scores.php');

function teeboxes() {
	global $db, $user_prefix, $admin, $user;
	
	$teebox_id=$_GET['teebox_id'];
	$course_id=$_GET['course_id'];
	if (!$course_id) return;
	
	$sql = 'select gc.* from '.$user_prefix.'_golf_courses gc 
		where course_id='.$course_id;
    $result = $db->sql_query($sql);
	//echo '<pre>'.$sql.'</pre>';
    if ($db->sql_numrows($result)) {
		$course_row = $db->sql_fetchrow($result);
	}
	
	$s = '<table align="center">';
	
	$s .= '<tr><td><table align="center"><tr><td><b>'.ui_form_safe($course_row['course_name']).' Tee Boxes</b></td></tr></table></td></tr>';
	
	$s .= '<tr><td>';
	$s .= '<table border="1px solid black" cellpadding=4 cellspacing=0 align="center">';
	$s .= '<tr><td><b>Tee box</b></td><td><b>Rating/Slope</b></td><td><b>Distance</b></td><td><b>Holes</b></td><td><b>Submitted By</b></td>'.
		((is_user($user) or $admin)?'<td>edit</td>':'').
		($admin?'<td>delete</td>':'').
		'<td>&nbsp;</td></tr>';
	
	$sql = 'select gct.*, username from '.$user_prefix.'_golf_course_teeboxes gct
		left join '.$user_prefix.'_users u on u.user_id=gct.teebox_added_by
		where course_id='.$course_id;
	//echo '<pre>'.$sql.'</pre>';
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		while ($row = $db->sql_fetchrow($result)) {
			$s .= '<tr><td>'.ui_form_safe($row['teebox_name']).'&nbsp;</td><td>'.sprintf('%.1f',$row['teebox_rating']).'/'.$row['teebox_slope'].'&nbsp;</td>'.
			'<td>&nbsp;</td>'.
			'<td><a href="/modules.php?name=Golf&op=teeboxes_holes&teebox_id='.$row['teebox_id'].'&course_id='.$course_id.'">holes</a></td>'.
			'<td>'.ui_form_safe(ucwords($row['username'])).'</td>'.
			($admin?'<td><a href="/modules.php?name=Golf&op=teeboxes&teebox_id='.$row['teebox_id'].'&course_id='.$course_id.'">edit</a></td>':'').
			($admin?'<td><a href="/modules.php?name=Golf&op=teeboxes_delete&teebox_id='.$row['teebox_id'].'&course_id='.$course_id.'">delete</a></td>':'').
			'<td><a href="/modules.php?name=Golf&op=players_scores&course_id='.$course_id.'&teebox_id='.$row['teebox_id'].'">enter score</a></td></tr>';
			if ($row['teebox_id'] == $teebox_id) {
				$edit_row = $row;
			}
		}
	}
	$s .= '</td></tr></table>';
	$s .= '</td></tr>';
	
	if (is_user($user) || $admin) {
		$s .= '<tr><td><table align="center"><tr><td><b>'.(isset($edit_row)?'Edit':'Add').' Tee Box</b></td></tr></table></td></tr>';
		
		$s .= 
			'<tr><td>
			<form name="teeboxes" action="/modules.php?name=Golf&op=teeboxes_addedit" method="POST"/>
			<input type="hidden" name="teebox_id" value="'.$teebox_id.'"/>
			<input type="hidden" name="course_id" value="'.$course_id.'"/>
			<input type="hidden" name="teebox_added_by" value="'.(isset($edit_row)?$edit_row['teebox_added_by']:get_user_id()).'"/>
			<table align="center">
				<tr><td align="right">Name:</td><td><input type="text" name="teebox_name" value="'.(isset($edit_row)?ui_form_safe($edit_row['teebox_name']):'').'" size="32" maxlength="32"/></td></tr>
				<tr><td align="right">Rating:</td><td><input type="text" name="teebox_rating" value="'.(isset($edit_row)?sprintf('%.1f',$edit_row['teebox_rating']):'').'" size="10" maxlength="10"/> ex: 71.2</td></tr>
				<tr><td align="right">Slope:</td><td><input type="text" name="teebox_slope" value="'.(isset($edit_row)?$edit_row['teebox_slope']:'').'" size="10" maxlength="10"/> ex: 124</td></tr>
				<tr><td align="right">Distance:</td><td>&nbsp;</td></tr>
				<tr><td align="right">Course:</td><td>'.(isset($course_row)?ui_form_safe($course_row['course_name']):'').'</td></tr>
				<tr><td align="right"></td><td><input type="submit" value="'.(isset($edit_row)?'update':'add').'"/></td></tr>
			</table>
			</form>
			</td></tr>';
	}
	$s .= '<tr><td><table align="center"><tr><td><b>Who Have Played Here</b></td></tr></table></td></tr>';
	$s .= '<tr><td align="center">'.scores_show().'</td></tr>';
	$s .= '</table>';
	echo $s;
	
}
function teeboxes_addedit() {
	global $user, $admin, $db, $user_prefix, $user_info;
	$teebox_id = $_POST['teebox_id'];
	$course_id = $_POST['course_id'];
	if(!( is_user($user) || $admin) ) {
		echo '<span class="error">Registered user only, please register and login.</span>';
		return;  
	}
	/*if (is_user($user) && $teebox_id) {
		echo '<span class="error">Registered user can add only, notify admin to edit courses</span>';
		return;  
	}*/
	$teebox_added_by = $_POST['teebox_added_by'];
	if ($teebox_id && !$admin && $teebox_added_by != $user_info['user_id']) {
		echo '<span class="error">Cannot edit teeboxes not added by you.</span>';
		return;  
	}
	
	if ($teebox_id) {
		$sql = 'UPDATE '.$user_prefix.'_golf_course_teeboxes SET 
			teebox_name="'.addslashes($_POST['teebox_name']).'",
			teebox_slope="'.FixQuotes($_POST['teebox_slope']).'",
			teebox_rating="'.FixQuotes($_POST['teebox_rating']).'" WHERE teebox_id='. $teebox_id;
	} else {
		$teebox_added_by = get_user_id();
		$sql = 'INSERT INTO '.$user_prefix.'_golf_course_teeboxes (teebox_name, teebox_slope, teebox_rating, teebox_added_by, course_id)
			VALUES ("'.addslashes($_POST['teebox_name']).'",
			"'.FixQuotes($_POST['teebox_slope']).'","'.FixQuotes($_POST['teebox_rating']).'","'.$teebox_added_by.'","'.FixQuotes($course_id).'")';
	}
	
	$result = $db->sql_query($sql);
    if(!$result) {
		echo $sql;
    	echo ""._ERROR."<br>";
		exit();
    }
	header('Location: /modules.php?name=Golf&op=teeboxes&course_id='.$course_id);
	
}
function teeboxes_delete() {
	global $admin, $db, $user_prefix;
	if(!$admin) {
		echo '<span class="error">Admin only!</span>';
		return; 
	}
	$teebox_id = $_GET['teebox_id'];
	$course_id = $_POST['course_id'];
	if ($teebox_id) {
		//TODO: check dependencies
		$sql = 'DELETE FROM '.$user_prefix.'_golf_course_teeboxes WHERE teebox_id='.$teebox_id.' LIMIT 1';
		$result = $db->sql_query($sql);
		if(!$result) {
			if ($admin) {
				echo ""._ERROR."<br>";
				exit();
			}
		}
	}
	header('Location: /modules.php?name=Golf&op=teeboxes&course_id='.$course_id);
}
function teeboxes_holes() {
	global $db, $user_prefix, $admin, $user;
	if(!( is_user($user) || $admin) ) {
		echo '<span class="error">Registered user only, please register and login.</span>';
		return;  
	}
	
	$allow_edit = false;
	$user_id = get_user_id();
	$course_id=$_GET['course_id'];
	if (!$course_id) return;

	$sql = 'select course_name from '.$user_prefix.'_golf_courses gs where gs.course_id='.$course_id;
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$row = $db->sql_fetchrow($result);
		$course_name = $row['course_name'];
	}

	$s = '
	<form name="course_holes" method="post" action="/modules.php?name=Golf&op=teeboxes_holes_addedit&course_id='.$course_id.'">
	<table align="center" cellpadding="0" cellspacing="0">';
	
	$s .= '<tr><td><table align="center"><tr><td><b>'.ui_form_safe($course_name).' Holes Detail</b></td></tr></table></td></tr>';
	
	// hole number
	$s .= '
	<tr><td>
	<table border="solid 1px" cellpadding="1" cellspacing="0">
	<tr><td>Hole</td><td>&nbsp;</td>';
	for ($i=1;$i<=18;$i++) {
		$s .= '<td>'.$i.'</td>';
		if ($i == 9) {
			$s .= '<td>Out</td>';
		}
	}
	$s .= '<td>In</td><td>Total</td>';
	$s .= '</tr>';
	
	$have_holes = true;
	$sql = 'select gct.*, gc.course_name, gc.course_added_by from '.$user_prefix.'_golf_course_teeboxes gct 
		left join '.$user_prefix.'_golf_courses gc on gct.course_id=gc.course_id where gct.course_id='.$course_id;
    $result = $db->sql_query($sql);
    if ($db->sql_numrows($result)) {
		$teebox_count = 0;
		while ($teebox_row = $db->sql_fetchrow($result)) {
			$teebox_count++;
			
			$hcprow = '<tr><td>Handicap</td><td>&nbsp;</td>';
			$prow = '<tr><td>Par</td><td>&nbsp;</td>';
			
			// hole info
			$drow = '
			<tr><td>'.ui_form_safe($teebox_row['teebox_name']).'<input type="hidden" name="teebox_id_'.$teebox_count.'" value="'.$teebox_row['teebox_id'].'"/></td>';
			$drow .= '<td>'.sprintf('%.1f',$teebox_row['teebox_rating']).'/'.$teebox_row['teebox_slope'].'</td>';
			
			$sql = 'select * from '.$user_prefix.'_golf_holes where teebox_id='.$teebox_row['teebox_id'].' order by hole_number';
			$hresult = $db->sql_query($sql);
			if ($db->sql_numrows($hresult)) {
				$out = 0; $in = 0;
				$pout = 0; $pin = 0;
				$h = 0;
				while ($hrow = $db->sql_fetchrow($hresult)) {
					$h++;
					if ($h < 10) {
						$out += $hrow['hole_distance'];
						$pout += $hrow['hole_par'];
					} else {
						$in += $hrow['hole_distance'];
						$pin += $hrow['hole_par'];
					}
					$hcprow .= '<td>';
					$prow .= '<td>';
					$drow .= '<td>
						<input type="hidden" name="hole_id_'.$hrow['teebox_id'].'_'.$hrow['hole_number'].'" value="'.$hrow['hole_id'].'"/>
						<input type="hidden" name="hole_handicap_'.$hrow['teebox_id'].'_'.$hrow['hole_number'].'" value="'.$hrow['hole_handicap'].'"/>';
						if ($user_id == $teebox_row['teebox_added_by'] || $user_id == $teebox_row['course_added_by'] || $admin) {
							$allow_edit = true;
							$drow .= '<input type="text" name="hole_distance_'.$hrow['teebox_id'].'_'.$hrow['hole_number'].'" value="'.$hrow['hole_distance'].'" size="3" maxlength="3" onClick="javascript:this.select();"/>';
							$prow .= '<input type="text" name="hole_par_'.$hrow['teebox_id'].'_'.$hrow['hole_number'].'" value="'.$hrow['hole_par'].'" size="3" maxlength="1" onClick="javascript:this.select();"/>';
							$hcprow .= '<input type="text" name="hole_handicap_'.$hrow['teebox_id'].'_'.$hrow['hole_number'].'" value="'.$hrow['hole_handicap'].'" size="3" maxlength="2" onClick="javascript:this.select();"/>';
						} else {
							$drow .= '<input type="hidden" name="hole_distance_'.$hrow['teebox_id'].'_'.$hrow['hole_number'].'" value="'.$hrow['hole_distance'].'"/>';
							$drow .= $hrow['hole_distance'];
							$prow .= $hrow['hole_par'];
							$hcprow .= $hrow['hole_handicap'];
						}
						$hcprow .= '</td>';
						$prow .= '</td>';
						$drow .= '</td>';
						if ($h == 9) {
							$hcprow .= '<td>&nbsp;</td>';
							$prow .= '<td><b>'.$pout.'</b></td>';
							$drow .= '<td><b>'.$out.'</b></td>';
						}
				}
				$drow .= '<td><b>'.$in.'</b></td><td><b>'.($out + $in).'</b></td>';
				$prow .= '<td><b>'.$pin.'</b></td><td><b>'.($pout + $pin).'</b></td>';
				$hcprow .= '<td>&nbsp;</td><td>&nbsp;</td>';
			} else {
				$hcprow .= '<td colspan="21">&nbsp;</td>';
				$prow .= '<td colspan="21">Create hole details only if you plan to enter scores hole-by-hole.</td>';
				$drow .= '<td colspan="21">&nbsp;</td>';
				$have_holes = false;
				$allow_edit = true;
			}
			$hcprow .= '</tr>';
			$prow .= '</tr>';
			$drow .= '</tr>';
			if ($teebox_count == 1) {
				$hcrow = $hcprow; // only show handicap of 1st tee box
				$s .= $prow;	// only show par for 1st tee box
			}
			$s .= $drow;
		}
	}
	$s .= $hcrow;
	$s .= '</table>
	</td></tr>';
	if ($allow_edit) {	
		$s .= '<tr><td><input type="submit" value="'.($have_holes?'update':'create hole details').'"/></td></tr>';
	}
	$s .= '</table>
	</form>';
	echo $s;
}
function teeboxes_holes_addedit() {
	global $db, $user_prefix, $admin, $user;
	if(!( is_user($user) || $admin) ) {
		echo '<span class="error">Registered user only, please register and login.</span>';
		return;  
	}
	$course_id=$_GET['course_id'];
	$hole_pars = array();
	$hole_hcps = array();
	
	$i = 0;
	while (true) {
		$i++;
		$teebox_id = $_POST['teebox_id_'.$i];
		if ($teebox_id > 0) {
			$result = $db->sql_query('select * from '.$user_prefix.'_golf_holes where teebox_id='.$teebox_id);
			if ($db->sql_numrows($result)) {
				for ($h=1; $h<=18; $h++) {
					if (!isset($hole_pars[$h])) {
						$hole_pars[$h] = 0;
						$hole_hcps[$h] = 0;
					}
					$hole_id = $_POST['hole_id_'.$teebox_id.'_'.$h];
					if ($hole_id > 0) {
						$hole_handicap = $_POST['hole_handicap_'.$teebox_id.'_'.$h];
						$hole_distance = $_POST['hole_distance_'.$teebox_id.'_'.$h];
						$hole_par = $_POST['hole_par_'.$teebox_id.'_'.$h];
						if ($hole_par > 0) {
							$hole_pars[$h] = $hole_par;	// 1st tee box listed has par info
						} else {
							$hole_par = $hole_pars[$h];
						}
						$hole_hcp = $_POST['hole_handicap_'.$teebox_id.'_'.$h];
						if ($hole_hcp > 0) {
							$hole_hcps[$h] = $hole_hcp;	// 1st tee box listed has hcp info
						} else {
							$hole_hcp = $hole_hcps[$h];
						}
						$sql = 'update '.$user_prefix.'_golf_holes set hole_handicap='.$hole_hcp.','.
							' hole_distance='.$hole_distance.', hole_par='.$hole_par.
							' where hole_id='.$hole_id;
						$result = $db->sql_query($sql);
						//echo '<pre>'.$sql.'</pre>';
					}
				}
			} else {
				for ($h=1; $h<=18; $h++) {
					$sql = 'insert into '.$user_prefix.'_golf_holes (hole_number, teebox_id) values ("'.$h.'","'.$teebox_id.'")';
					$result = $db->sql_query($sql);
					if(!$result) {
						if ($admin) {
							echo ""._ERROR." " . $sql ."<br>";
							exit();
						}
					}
				}
			}
		} else {
			break;
		}
	}
	header('Location: /modules.php?name=Golf&op=teeboxes_holes&course_id='.$course_id);
}
?>
