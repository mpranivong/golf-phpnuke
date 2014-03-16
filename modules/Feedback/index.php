<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2005 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* Based on Feedback Addon 1.0                                          */
/* Copyright (c) 2001 by Jack Kozbial (jack@internetintl.com)           */
/* http://www.InternetIntl.com                                          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!defined('MODULE_FILE')) {
	die ("You can't access this file directly...");
}

require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

/**********************************/
/* Configuration                  */
/*                                */
/* You can change this:           */
/* $index = 0; (right side off)   */
/**********************************/
define('INDEX_FILE', true);
$subject = $sitename." "._FEEDBACK;
/**********************************/

define('NO_EDITOR', true);
include("header.php");

if (!isset($opi) OR ($opi != "ds")) {
  $intcookie = intval($cookie[0]);
  if (!empty($cookie[1])) {
    $sql = "SELECT name, username, user_email FROM ".$user_prefix."_users WHERE user_id='".$intcookie."'";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);
    if (!empty($row['name'])) {
		$sender_name = filter($row['name'], "nohtml");
	} else {
		$sender_name = filter($row['username'], "nohtml");
	}
	$sender_email = filter($row['user_email'], "nohtml");
  } else {
    $sender_email = "";
    $sender_name = "";
  }
}

if (!isset($message)) { $message = ""; }
if (isset($_GET['signup'])) {
    $message .= "Sign me up.\n";
    $message .= "Tournament: ".$_GET['msg']."\n";
    $message .= "Flight A,B,C: \n";
    $message .= "Player Phone #: \n";
    if ($_GET['p'] >= 2) {
        $message .= "Player 2 Phone #: \n";
    }
    $subject = "Tournament Signup";
}

if (!isset($opi)) { $opi = ""; }
if (!isset($send)) { $send = ""; }
require_once('recaptchalib.php');
$publickey = '6LcAq8ESAAAAAJpBidccXWWJGKrDHnJG9xkoeSFl';
$form_block = "
    <center><font class=\"title\"><b>$sitename: "._FEEDBACKTITLE."</b></font>
    <br><br><font class=\"content\">"._FEEDBACKNOTE."</font>
    <FORM METHOD=\"post\" ACTION=\"modules.php?name=$module_name&no_header=1\">
    <P><strong>"._YOURNAME.":</strong><br>
    <INPUT type=\"text\" NAME=\"sender_name\" VALUE=\"$sender_name\" SIZE=30></p>
    <P><strong>"._YOUREMAIL.":</strong><br>
    <INPUT type=\"text\" NAME=\"sender_email\" VALUE=\"$sender_email\" SIZE=30></p>
    <P><strong>"._MESSAGE.":</strong><br>
    <TEXTAREA NAME=\"message\" COLS=70 ROWS=15 WRAP=virtual>$message</TEXTAREA></p>
    <P>Enter verification words separated with a space<br>
    ".recaptcha_get_html($publickey)."<br>
    <!--INPUT type=\"text\" NAME=\"test\" VALUE=\"\" SIZE=30--></p>
    <i>"._HTMLNOTALLOWED2."</i>
    <INPUT type=\"hidden\" name=\"opi\" value=\"ds\">
    <P><INPUT TYPE=\"submit\" NAME=\"submit\" VALUE=\""._SEND."\"></p>
    </FORM></center>
";

OpenTable();
if ($_POST['opi'] != "ds") {
    echo $form_block;
} else {
    if (empty($sender_name)) {
	$name_err = "<div align=\"center\" color=\"red\"><span class=\"option\"><strong><em>"._FBENTERNAME."</em></strong></span></div><br>";
	$send = "no";
    } 
    if (empty($sender_email) || $send_email == "email@gmail.com") {
	$email_err = "<div align=\"center\" color=\"red\"><span class=\"option\"><strong><em>"._FBENTEREMAIL."</em></strong></span></div><br>";
	$send = "no";
    } 
    if (empty($message)) {
    	$message_err = "<div align=\"center\" color=\"red\"><span class=\"option\"><strong><em>"._FBENTERMESSAGE."</em></span></font></div><br>";
    	$send = "no";
    }
    $privatekey = '6LcAq8ESAAAAABwxvQ11kEn1rg9vAVcz2dXql5z9';
    $resp = recaptcha_check_answer($privatekey, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
    if (!$resp->is_valid) { //$test != "3651") { 
        $message_err = "<br><div align=\"center\" color=\"red\"><span class=\"option\"><strong><em>Invalid Verification Words!</em></span></font></div><br>";
        $send = "no";
    }
	if ($send != "no") {
		$sender_name = removecrlf(filter($sender_name, "nohtml"));
		$sender_email = removecrlf(filter($sender_email, "nohtml"));
		$message = filter($message, "nohtml");
		$msg = "$sitename\n\n";
		$msg .= ""._SENDERNAME.": $sender_name\n";
		$msg .= ""._SENDEREMAIL.": $sender_email\n";
		$msg .= ""._MESSAGE.": $message\n\n";
		$to = $adminmail;
		$mailheaders = "From: $sender_name <$sender_email>\n";
		$mailheaders .= "Reply-To: $sender_email\n\n";
		mail($to, $subject, $msg, $mailheaders);
	echo "<p><div align=\"center\">"._FBMAILSENT."</div></p>";
	echo "<p><div align=\"center\">"._FBTHANKSFORCONTACT."</div></p>";
    } elseif ($send == "no") {
	OpenTable2();
	if (!empty($name_err)) { echo "$name_err"; }
	if (!empty($email_err)) {echo "$email_err"; }
	if (!empty($message_err)) {echo "$message_err"; }
	CloseTable2();
	echo "<br><br>";
	echo $form_block;
	}
}

CloseTable();
include("footer.php");

?>