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
function courses() {
	
	global $db, $user_prefix, $admin, $user;
	
	$course_id=$_GET['course_id'];
	
	$s = '<table align="center">';
	if (!$course_id) {
		$s .= '<tr><td><table align="center"><tr><td><b>Golf Courses</b></td></tr></table></td></tr>';
		$s .= '<tr><td><table align="center"><tr><td><font size="1">(for courses with more then 18 holes, each 18 holes are listed separately)</font></td></tr></table></td></tr>';
		$s .= '<tr><td>';
		$s .= '<table border="1px solid black" cellpadding=4 cellspacing=0 align="center">';
		$s .= '<tr><td><b>Course</b></td><td><b>Website</b></td><td><b>City</b></td><td><b>Phone</b></td><td>tee boxes/holes</td>'.
			((is_user($user) or $admin)?'<td>edit</td>':'').
			($admin?'<td>delete</td>':'').
			'<td>&nbsp;</td><td>Submitted By</td></tr>';
		
		$sql = 'select gc.*, username from '.$user_prefix.'_golf_courses gc
			left join '.$user_prefix.'_users u on u.user_id=gc.course_added_by
			order by course_name';
		$result = $db->sql_query($sql);
		if ($db->sql_numrows($result)) {
			while ($row = $db->sql_fetchrow($result)) {
				$s .= '<tr><td><a href="/modules.php?name=Golf&op=courses&course_id='.$row['course_id'].'">'.ui_form_safe($row['course_name']).'</a></td>';
				$s .= '<td>'.(strlen($row['course_url'])?'<a href="'.$row['course_url'].'" target="_blank">website</a>':'&nbsp;');
				$s .= '</td><td>'.ui_form_safe($row['course_city']).'&nbsp;'.'<a href="http://www.google.com/maps?f=q&hl=en&q='.urlencode($row['course_address'] . ', ' . $row['course_city'] . ', ' . $row['course_state']).'" target="_blank">Map</a></td>'.
				'<td>'.ui_form_safe($row['course_phone']).'&nbsp;</td>'.
				'<td><a href="/modules.php?name=Golf&op=teeboxes&course_id='.$row['course_id'].'">tee boxes/holes</a></td>'.
				'<td>'.($admin?'<a href="/modules.php?name=Golf&op=courses&course_id='.$row['course_id'].'">edit</a>':'').'&nbsp;</td>'.
				($admin?'<td><a href="/modules.php?name=Golf&op=courses_delete&course_id='.$row['course_id'].'">delete</a></td>':'').
				'<td><a href="/modules.php?name=Golf&op=players_scores&course_id='.$row['course_id'].'">enter score</a></td>'.
				'<td>'.(strlen($row['username'])?ui_form_safe(ucwords($row['username'])):'Admin').'&nbsp;</td></tr>';
			}
		}
		$s .= '</td></tr></table>';
		$s .= '</td></tr>';
	} else {
		$sql = 'select * from '.$user_prefix.'_golf_courses where course_id='.$course_id;
		$result = $db->sql_query($sql);
		if ($db->sql_numrows($result)) {
			$edit_row = $db->sql_fetchrow($result);
		}
	}
	if (is_user($user) || $admin) {
		$s .= '<tr><td><table align="center"><tr><td><b>'.(isset($edit_row)?'Edit':'Add').' Golf Course</b></td></tr></table></td></tr>';
		
		$s .= 
			'<tr><td>
			<form name="courses" action="/modules.php?name=Golf&op=courses_addedit" method="POST"/>
			<input type="hidden" name="course_id" value="'.$course_id.'"/>
			<input type="hidden" name="course_added_by" value="'.(isset($edit_row)?$edit_row['course_added_by']:get_user_id()).'"/>
			<table align="center">
				<tr><td align="right">Name:</td><td><input type="text" name="course_name" value="'.(isset($edit_row)?ui_form_safe($edit_row['course_name']):'').'" size="60" maxlength="100"/></td></tr>
				<tr><td align="right">Address:</td><td><input type="text" name="course_address" value="'.(isset($edit_row)?ui_form_safe($edit_row['course_address']):'').'" size="32" maxlength="64"/></td></tr>
				<tr><td align="right">City:</td><td><input type="text" name="course_city" value="'.(isset($edit_row)?ui_form_safe($edit_row['course_city']):'').'" size="32" maxlength="32"/></td></tr>
				<tr><td align="right">State</td><td><input type="text" name="course_state" value="'.(isset($edit_row)?ui_form_safe($edit_row['course_state']):'').'" size="2" maxlength="3"/></td></tr>
				<tr><td align="right">Phone</td><td><input type="text" name="course_phone" value="'.(isset($edit_row)?ui_form_safe($edit_row['course_phone']):'').'" size="20" maxlength="32"/></td></tr>
				<tr><td align="right">Website</td><td><input type="text" name="course_url" value="'.(isset($edit_row)?ui_form_safe($edit_row['course_url']):'').'" size="64" maxlength="64"/></td></tr>
				<tr><td align="right">Note</td><td><input type="text" name="course_note" value="'.(isset($edit_row)?ui_form_safe($edit_row['course_note']):'').'" size="64" maxlength="100"/></td></tr>';
				if ($course_id) {
					$s .= '<tr><td align="right">Tee Boxes</td><td><a href="/modules.php?name=Golf&op=teeboxes&course_id='.$course_id.'">tee boxes/holes</a></td></tr>';
				}
		$s .='<tr><td align="right"></td><td><input type="submit" value="'.(isset($edit_row)?'update':'add').'"/></td></tr>
			</table>
			</form>
			</td></tr>';
	}
	if ($course_id) {
		$s .= '<tr><td><table align="center"><tr><td><b>Who Have Played Here</b></td></tr></table></td></tr>';
		$s .= '<tr><td align="center">'.scores_show(0, 5).'</td></tr>';
	}
	$s .= '</table>';
	echo $s;
}
function courses_delete() {
	global $admin, $db, $user_prefix;
	if(!$admin) {
		echo '<span class="error">Admin only!</span>';
		return; 
	}
	$course_id = $_GET['course_id'];
	if ($course_id) {
		//TODO: check if course is used in tournaments and tee box
		$sql = 'DELETE FROM '.$user_prefix.'_golf_courses WHERE course_id='.$course_id.' LIMIT 1';
		$result = $db->sql_query($sql);
		if(!$result) {
			if ($admin) {
				echo ""._ERROR."<br>";
				exit();
			}
		}
	}
	header('Location: /modules.php?name=Golf&op=courses');
}
function courses_addedit() {
	global $user, $admin, $db, $user_prefix, $user_info;
	$course_id = $_POST['course_id'];
	if(!( is_user($user) || $admin) ) {
		echo '<span class="error">Registered user only, please register and login.</span>';
		return;  
	}
	$course_added_by = $_POST['course_added_by'];
	if ($course_id && !$admin && $course_added_by != $user_info['user_id']) {
		echo '<span class="error">Cannot edit courses not added by you.</span>';
		return;  
	}
	
	$course_url = strtolower(trim($_POST['course_url']));
	if (strlen($course_url)) {
		if (substr($course_url,0,7) != 'http://' && substr($course_url,0,8) != 'https://') {
			$course_url = 'http://'.$course_url;
		}
	}
	if ($course_id) {
		$sql = 'UPDATE '.$user_prefix.'_golf_courses SET 
			course_name="'.FixQuotes($_POST['course_name']).'",
			course_address="'.FixQuotes($_POST['course_address']).'",
			course_city="'.FixQuotes($_POST['course_city']).'",
			course_state="'.strtoupper(FixQuotes($_POST['course_state'])).'",
			course_phone="'.FixQuotes($_POST['course_phone']).'",
			course_url="'.FixQuotes($course_url).'",
			course_note="'.FixQuotes($_POST['course_note']).'" WHERE course_id='. $course_id;
	} else {
		$course_added_by = get_user_id();
		$sql = 'INSERT INTO '.$user_prefix.'_golf_courses (course_name, course_address, course_city, course_state, course_phone,
			course_url, course_note, course_added_by)
			VALUES ("'.FixQuotes($_POST['course_name']).'",
			"'.FixQuotes($_POST['course_address']).'","'.FixQuotes($_POST['course_city']).'","'.strtoupper(FixQuotes($_POST['course_state'])).'",
			"'.FixQuotes($_POST['course_phone']).'","'.FixQuotes($course_url).'","'.FixQuotes($_POST['course_note']).'",'.$course_added_by.')';
	}
	$result = $db->sql_query($sql);
    if(!$result) {
    	echo ""._ERROR."<br>";
		exit();
    }
	if (!$course_id) {
		$course_id = $db->sql_nextid();
		header('Location: /modules.php?name=Golf&op=teeboxes&course_id='.$course_id);
	} else {
		header('Location: /modules.php?name=Golf&op=courses');
	}
}
?>
