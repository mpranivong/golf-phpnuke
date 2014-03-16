<?php

#########################################################
# Weatherford portal theme for PHPNuke 5.0                             #
#########################################################

?>

<script type="text/javascript">
    var GB_ROOT_DIR = "includes/greybox/";
</script>
<script type="text/javascript" src="includes/greybox/AJS.js"></script>
<script type="text/javascript" src="includes/greybox/AJS_fx.js"></script>
<script type="text/javascript" src="includes/greybox/gb_scripts.js"></script>
<link href="includes/greybox/gb_styles.css" rel="stylesheet" type="text/css" />

<?php

$thename = "DFWLGA";

define('DFWLGA_MAX_WIDTH', "900");
define('DFWLGA_LEFTCOL_WIDTH', "130");
define('DFWLGA_CENTERCOL_WIDTH', "550"); //"410"); //245
define('DFWLGA_START_CENTERCOL_WIDTH', "550"); //"269"); //245
define('DFWLGA_ADMIN_CENTERCOL_WIDTH', "410");
define('DFWLGA_RIGHTCOL_WIDTH', "150");

$no_header = isset($_GET['no_header'])?$_GET['no_header']:0;

$lnkcolor = "#035D8A";
$bgcolor1 = "#FFFFFF";	//"#FFFFE6";
$bgcolor2 = $bgcolor1;//"#006699";
$bgcolor3 = $bgcolor1;//"#FFFFE6";
$bgcolor4 = $bgcolor1;//"#FFC53A";
//$bgcolor4 = "#669933";
$textcolor1 = "FFFFFF";
$textcolor2 = "000000";
$hr = 1; # 1 to have horizonal rule in comments instead of table bgcolor

function OpenTable() {
    global $bgcolor1, $bgcolor2;
	$width = get_center_width();	
    echo "<table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$bgcolor2\"><tr><td>\n";
    echo "<table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$bgcolor1\"><tr><td>\n";
}

function OpenTable2() {
    global $bgcolor1, $bgcolor2;
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$bgcolor2\" align=\"center\"><tr><td>\n";
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$bgcolor1\"><tr><td>\n";
}

function CloseTable() {
    echo "</td></tr></table></td></tr></table>\n";
}

function CloseTable2() {
    echo "</td></tr></table></td></tr></table>\n";
}

function FormatStory($thetext, $notes, $aid, $informant) {
    global $anonymous;
    if ($notes != "") {
	$notes = "<b>"._NOTE."</b> <i>$notes</i>\n";
    } else {
	$notes = "";
    }
    if ("$aid" == "$informant") {
	echo "<font size=\"2\" color=\"#505050\">$thetext<br>$notes</font>\n";
    } else {
	if($informant != "") {
	    $boxstuff = "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&amp;username=$informant\">$informant</a> ";
	} else {
	    $boxstuff = "$anonymous ";
	}
	$boxstuff .= ""._WRITES."  <i>\"$thetext\"</i> $notes\n";
	echo "<font size=\"2\" color=\"#505050\">$boxstuff</font>\n";
    }
}


function themeheader() {
    global $slogan, $sitename, $banners, $nukeurl, $no_header;
    $ThemeSel = get_theme();
	echo "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#035D8A\" vlink=\"#035D8A\">";
	if (!$no_header) {
		echo "
			<br><center>";
		echo "
			<table border=\"0\" width=\"". DFWLGA_MAX_WIDTH . "\" cellpadding=\"0\" cellspacing=\"0\"><tr><td colspan='3' align='center' height='159' width='800'>
			<table cellpadding=\"0\" cellspacing=\"0\" border=0>
			<tr><td colspan=\"8\">
			<a href=$nukeurl><img src='themes/$ThemeSel/images/header.gif' Alt=\""._WELCOMETO." $sitename\" border='0'></a>
			</td></tr>
			<tr>
				<td><a href=$nukeurl><img src='themes/$ThemeSel/images/header1.gif' Alt=\""._WELCOMETO." $sitename\" border='0'></a></td>
				<td><a href='modules.php?name=Golf&op=home'><img src='themes/$ThemeSel/images/hdr_home.gif' Alt=\"Home\" border='0'></a></td>
				<td><a href='modules.php?name=Golf&op=tournaments'><img src='themes/$ThemeSel/images/hdr_tourney.gif' Alt=\"Tournaments\" border='0'></a></td>
				<td><a href='modules.php?name=Golf&op=players'><img src='themes/$ThemeSel/images/hdr_player.gif' Alt=\"Players/Stats\" border='0'></a></td>
				<td><a href='modules.php?name=Golf&op=courses'><img src='themes/$ThemeSel/images/hdr_gc.gif' Alt=\"Golf Courses\" border='0'></a></td>
				<td><a href='modules.php?name=Golf&op=admin'><img src='themes/$ThemeSel/images/hdr_admin.gif' Alt=\"Admin\" border='0'></a></td>
				<td><a href='modules.php?name=Feedback&no_header=1' rel='gb_page[600,600]' title='Feedback'><img src='themes/$ThemeSel/images/hdr_contact.gif' Alt=\"Contact\" border='0'></a></td>
				<td><a href='".$_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'], '?')===false?'?':'&')."no_header=1'><img src='themes/$ThemeSel/images/header2.gif' Alt=\""._WELCOMETO." $sitename\" border='0'></a></td>
			</tr>
			</table>
			
			<tr><td colspan='3' background='themes/$ThemeSel/images/pixel.gif' width='900' height='3'></td></tr>
			</table>";
	
		echo "
			<table width=\"".DFWLGA_MAX_WIDTH."\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tr>";
		if (!is_forum_page()) {
			echo "<td valign=\"top\" width=\"".DFWLGA_LEFTCOL_WIDTH."\">\n";
			blocks(left);
		}
		$width = get_center_width();
	}	
	echo "<td valign='top' width='100%' align='center'>\n\n\n";
}

function themefooter() {
    global $ThemeSel, $admin, $no_header;
    $ThemeSel = get_theme();

	if (!is_forum_page()) {    
		echo "</td>";//<td valign='top' background='themes/$ThemeSel/images/separator_3.jpg' width='8' height='3'></td>";
		if (is_home_page()) {
			echo "<td valign=\"top\" width=\"".DFWLGA_RIGHTCOL_MAX."\">\n";
			blocks(right);
			echo "</td>";
			//echo "</td><td valign='top' background='themes/$ThemeSel/images/separator_4.jpg' width='10' height='3'></td>";
		}
		/*
		echo "<td rowspan='10' valign='top' width='145'>
		<form action='http://webmail.uwmail.com/cgi-bin/openwebmail/openwebmail.pl' method='post' name='uwwebmail'/>
		<INPUT TYPE='hidden' NAME='logindomain' VALUE='webmail.uwmail.com'>";
		echo '<table cellspacing="0" cellpadding="0" border="0" width="145">
		<tr><td colspan="3" align="right"><img width="145" height="69" src="themes/'.$ThemeSel.'/images/webmail_login1.jpg"></td></tr>
		<tr>
		<td><img width="37" height="18" src="themes/'.$ThemeSel.'/images/webmail_login2.jpg"></img></td>
		<td align="left" width="88" bgcolor="#66CC33"><input type="text" name="loginname" size="14"/></td>
		<td align="right"><img width="20" height="18" src="themes/'.$ThemeSel.'/images/webmail_login3.jpg"></td>
		</tr>
		<tr><td colspan="3" width="145" height="4" background="themes/'.$ThemeSel.'/images/webmail_login4.jpg"></td></tr>
		<tr>
		<td><img width="37" height="18" src="themes/'.$ThemeSel.'/images/webmail_login5.jpg"></td>
		<td align="left" width="88" bgcolor="#66CC33"><input type="password" name="password" size="14"/></td>
		<td width="20" height="18" align="right"><a href="javascript:document.uwwebmail.submit();" title="login to webmail"><img src="themes/'.$ThemeSel.'/images/webmail_login6.jpg" width="20" height="18" border="0"></img></a></td>
		</tr>
		</form>
		<tr><td colspan="3" align="right"><img width="145" height="11" src="themes/'.$ThemeSel.'/images/webmail_login7.jpg"></td></tr>
		</table>
		</td>';
		*/
		//echo "<td rowspan='10' valign='top' align='left' background='themes/$ThemeSel/images/header_10.jpg' width='50' height='3'><img src='themes/$ThemeSel/images/header_9.jpg' width='50' height='120'></img></td>";
		//}
	}
    echo "</tr></table></td></tr></table>";
	if (!$no_header) {
		footmsg();
	}
}


function themeindex ($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext) {
    global $anonymous, $tipath;
    $ThemeSel = get_theme();
    /*
    mt_srand((double)microtime()*1000000);
    $rcolor = mt_rand(1, 3);
    if ($rcolor == 1) {
	$tcolor = "66CC33";
    } elseif ($rcolor == 2) {
	$tcolor = "003399";
    } elseif ($rcolor == 3) {
	$tcolor = "FF6633";
    }
    */
    if (file_exists("themes/$ThemeSel/images/topics/$topicimage")) {
	$t_image = "themes/$ThemeSel/images/topics/$topicimage";
    } else {
	$t_image = "$tipath$topicimage";
    }
	$width = get_center_width();
    
    echo "<table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=035D8A><tr><td>\n";
    echo "<table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=FFFFFF><tr><td>\n";
    echo "<a href=modules.php?name=News&amp;new_topic=$topic><img src=$t_image Alt=\"$topictext\" border=0 align=right></a>\n";
    echo "<img src=\"themes/$ThemeSel/images/bullet.gif\" border=0 hspace=3><font size=3><b>$title</b></font><br>\n";
    echo "<font size=1 color=035D8A>"._POSTEDBY." ";
    formatAidHeader($aid);
    echo " "._ON." $time $timezone ($counter "._READS.")<br><br></font>\n";
    if ("$aid" == "$informant") {
	echo "<font size=2 color=000000>$thetext</font><br><br>\n";
    } else {
	if ($informant != "") {
	    $boxstuff = "<a href=modules.php?name=Your_Account&amp;op=userinfo&username=$informant>$informant</a> ";
	} else {
	    $boxstuff = "$anonymous ";
	}
	$boxstuff .= ""._WRITES." <i>\"$thetext\"</i> $notes\n";
	echo "<font size=2 color=000000>$boxstuff</font><br><br>\n";
    }
    echo "<font size=2>$morelink</font><br><img src=images/line.gif border=0 vspace=4>\n";
    echo "</td></tr></table>\n";
    echo "</td></tr></table>\n";
    echo "<br>\n\n\n";
}



function themearticle($aid, $informant, $datetime, $title, $thetext, $topic, $topicname, $topicimage, $topictext) {
    global $admin, $sid, $tipath;
    $ThemeSel = get_theme();
    $module = $_SERVER['PHP_SELF'];
    if (file_exists("themes/$ThemeSel/images/topics/$topicimage")) {
	$t_image = "themes/$ThemeSel/images/topics/$topicimage";
    } else {
	$t_image = "$tipath$topicimage";
    }

	$width = get_center_width();
		
    echo "<table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=035D8A><tr><td>\n";
    echo "<table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=FFFFFF><tr><td>\n";
    echo "<a href=modules.php?name=News&amp;new_topic=$topic><img src=$t_image Alt=\"$topictext\" border=0 align=right></a>\n";
    echo "<img src=\"themes/$ThemeSel/images/bullet.gif\" border=0 hspace=3><font size=2><b>$title</b></font>\n";
    if ($admin) {
	echo "&nbsp;&nbsp; [ <a href=admin.php?op=EditStory&sid=$sid>"._EDIT."</a> | <a href=admin.php?op=RemoveStory&sid=$sid>"._DELETE."</a> ]<br>\n";
    } else {
	echo "<br>\n";
    }
    echo "<font size=1 color=035D8A>"._POSTEDBY."";
    formatAidHeader($aid);
    echo " "._ON." $datetime<br>\n";
    if ($informant != "") {
        echo ""._CONTRIBUTEDBY." <a href=modules.php?name=Your_Account&amp;op=userinfo&username=$informant>$informant</a><br><br>\n";
    } else {
	echo ""._CONTRIBUTEDBY." $anonymous<br><br></font>\n";
    }
    echo "<font size=2 color=000000>$thetext</font><br><br>\n";
    echo "</td></tr></table>\n";
    echo "</td></tr></table>\n\n";
}

function themesidebox($title, $content, $width=DFWLGA_RIGHTCOL_WIDTH, $bgcolor="#C7DFFB", $side='l') {
    $ThemeSel = get_theme();
    mt_srand((double)microtime()*1000000);
    $rcolor = mt_rand(1, 3);
    if ($side == 'l') {
	$tcolor = "2E6FBD";
    } elseif ($side == 'c') {
	$tcolor = "2E6FBD";
    } elseif ($side == 'r') {
	$tcolor = "2E6FBD";
    }
    echo "<table width=\"$width\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>\n";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\"><tr>";
    //echo "<!--td>\n<img src=\"themes/$ThemeSel/images/left$rcolor.gif\" alt=\"\" border=\"0\" width=\"5\" height=\"19\"></td-->\n";
    echo "<td bgcolor=$tcolor width=\"100%\"><font size=\"2\" color=\"#FFFFFF\">&nbsp;".strtoupper($title)."</font></td>\n";
    //echo "<!--td align=\"right\"><img src=\"themes/$ThemeSel/images/right$rcolor.gif\" alt=\"\" border=\"0\" width=\"5\" height=\"19\"></td-->";
    echo "</tr>";
    echo "<tr><td background='themes/$ThemeSel/images/header_shadow.jpg' width='$width' height='4' border='0'></td></tr></table>\n";
    echo "</td></tr><tr><td align=\"center\" valign=\"top\">\n";
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=$tcolor><tr><td width=100%>\n";
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=$tcolor><tr><td width=\"100%\" valign=\"top\" bgcolor=\"$bgcolor\">\n";
    echo "$content\n";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td>\n";
    echo "<img src=\"pixel.gif\" width=\"1\" height=\"4\" alt=\"\" border=\"0\"></td></tr></table>\n";
    echo "</td></tr></table>\n";
    echo "</td></tr></table>\n";
    echo "</td></tr><!--tr>\n";
    echo "<td align=\"center\" valign=\"bottom\">\n";
    echo "<img width=\"100%\" height=\"5\" src=\"themes/$ThemeSel/images/bottom$rcolor.gif\" vspace=\"0\" border=\"0\"></td></tr--></table>\n";
    echo "<br>\n\n\n";
}
function theme_message_box() {
    global $bgcolor1, $bgcolor2, $user, $admin, $cookie, $textcolor2, $prefix, $multilingual, $currentlang, $db;
    if ($multilingual == 1) {
	$querylang = "AND (mlanguage='$currentlang' OR mlanguage='')";
    } else {
	$querylang = "";
    }
    $sql = "SELECT mid, title, content, date, expire, view FROM ".$prefix."_message WHERE active='1' $querylang";
    $result = $db->sql_query($sql);
    if ($numrows = $db->sql_numrows($result) == 0) {
	return;
    } else {
	while ($row = $db->sql_fetchrow($result)) {
	    $mid = $row[mid];
	    $title = $row[title];
	    $content = $row[content];
	    $mdate = $row[date];
	    $expire = $row[expire];
	    $view = $row[view];
	if ($title != "" && $content != "") {
	    if ($expire == 0) {
		$remain = _UNLIMITED;
	    } else {
		$etime = (($mdate+$expire)-time())/3600;
		$etime = (int)$etime;
		if ($etime < 1) {
		    $remain = _EXPIRELESSHOUR;
		} else {
		    $remain = ""._EXPIREIN." $etime "._HOURS."";
		}
	    }
	    OpenTable();
	    if ($view == 4 AND is_admin($admin)) {
                $my_title = "<center><font class=\"option\" color=\"$textcolor2\"><b>$title</b></font></center>";
		$my_content = "<font class=\"content\">$content</font>"
		    ."<br><br><center><font class=\"content\">[ "._MVIEWADMIN." - $remain - <a href=\"admin.php?op=editmsg&mid=$mid\">"._EDIT."</a> ]</font></center>";
	    } elseif ($view == 3 AND is_user($user) || is_admin($admin)) {
                $my_title = "<center><font class=\"option\" color=\"$textcolor2\"><b>$title</b></font></center>";
		$my_content = "<font class=\"content\">$content</font>";
		if (is_admin($admin)) {
		    $my_content .= "<br><br><center><font class=\"content\">[ "._MVIEWUSERS." - $remain - <a href=\"admin.php?op=editmsg&mid=$mid\">"._EDIT."</a> ]</font></center>";
		}
	    } elseif ($view == 2 AND !is_user($user) || is_admin($admin)) {
                $my_title = "<center><font class=\"option\" color=\"$textcolor2\"><b>$title</b></font></center>";
		$my_content = "<font class=\"content\">$content</font>";
		if (is_admin($admin)) {
		    $my_content .= "<br><br><center><font class=\"content\">[ "._MVIEWANON." - $remain - <a href=\"admin.php?op=editmsg&mid=$mid\">"._EDIT."</a> ]</font></center>";
		}
	    } elseif ($view == 1) {
                $my_title = "<center><font class=\"option\" color=\"$textcolor2\"><b>$title</b></font></center>";
		$my_content = "<font class=\"content\">$content</font>";
		if (is_admin($admin)) {
		    $my_content .= "<br><br><center><font class=\"content\">[ "._MVIEWALL." - $remain - <a href=\"admin.php?op=editmsg&mid=$mid\">"._EDIT."</a> ]</font></center>";
		}
	    }
	    $my_title = $title;
		
		$width = get_center_width();
			
	    themesidebox($my_title, $my_content, $width, "#FFFFFF", 'c');
	    CloseTable();
	    echo "<br>";
	    if ($expire != 0) {
	    	$past = time()-$expire;
			if ($mdate < $past) {
				$db->sql_query("UPDATE ".$prefix."_message SET active='0' WHERE mid='$mid'");
			}
		}
	    }
	}
    }
}
function get_center_width () {
	if (is_home_page())
		$width = DFWLGA_START_CENTERCOL_WIDTH;
	else
		$width = DFWLGA_CENTERCOL_WIDTH;
	return $width;
}
function is_home_page () {
	//echo $_SERVER['PHP_SELF'] . ' ' . $_GET['name'];
	if ($_SERVER['PHP_SELF'] == 'modules.php' && (!isset($_GET['name'])) ) return true;
}
function is_forum_page () {
	return ($_GET['name'] == 'Forums');
}
?>