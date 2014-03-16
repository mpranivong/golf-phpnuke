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
if (eregi("golf.class.php", $_SERVER['PHP_SELF'])) {
	die ("You can't access this file directly...");
}

define('TOURNAMENT_ITEM_CLOSEST_TO_THE_PIN', 1);	// hole item type
define('TOURNAMENT_ITEM_LONG_DRIVE', 2);	// hole item type
$mod_version = '0.7.5';

$hole_contests = array(0=>'',1=>'Closest To Pin', 2=>'Long Drive', 3=>'Hole In One');
$hole_contests_short = array(0=>'',1=>'CTP', 2=>'LD', 3=>'HIO');
$member_titles = array(0=>'', 90=>'Pres', 80=>'VP', 70=>'Sec', 60=>'Treas', 50=>'HCP', 40=>'Web', 30=>'Web/HCP', 20=>'Adv');

function ui_select_box( &$arr, $name, $selected=0, $attribs='', $readonly=false ) {
	reset( $arr );
	$s = "\n<select name=\"$name\" $attribs".($readonly?' disabled ':'').">";
	foreach ($arr as $k => $v ) {
		$s .= "\n\t<option value=\"".$k."\"".($k == $selected ? " selected=\"selected\"" : '').">" . $v . "</option>";
	}
	$s .= "\n</select>\n";
	return $s;
}
function ui_form_safe( $str ) {
	return htmlspecialchars( stripslashes( $str ) );
}
//Returns Today,Yesterday, Day of week ('l')
//up to 6 days ago. Otherwise it uses $format
function date_relative($format,$epoch) {
   $day    = date('ymd',$epoch);
   $today  = date('ymd',time());
   $yday  = date('ymd',strtotime("-1 day"));
   $lastweek = date(strtotime("-6 day"));
   
   //account for last month's day
   /*switch($day) {
       case $today:    return "Today";                break;
       case $yday:    return "Yesterday";            break;
       default:*/       
           //Look up to one week ago
           if($epoch >= $lastweek) {
			   return(date('D',$epoch));
           }
          
           //If we made it to here, just print the format given
           return date($format,$epoch);
   //}
}
function html_mailer( $to, $subject, $body, $headers, $dept ) {
	global $sitename, $user_info, $golf_config, $nukeurl, $domain;
	
	$body = '<html><body style="font-family: arial; font-size: 12;">'.$body;
	$body .= "
		<hr>
		<table cellpadding='2' align='center'>
		<tr><td style='color:#669933'><b>$sitename $dept</b></td></tr>
		<tr><td style='font-size:9;' align='center'>Let's play golf!</td></tr>
		<tr><td style='font-size:9;' align='center'><a href='$nukeurl'>$domain</a></td></tr>
		<tr><td style='font-size:9;' align='center'>This message was sent by $domain. To be removed from this email list reply with Unscribe in the subject.</td></tr>
		</table>
		<hr>
		";
	$body .= '</body></html>';
	$headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n".$headers;
	if (mail($to, $subject, $body, $headers)) {
		return true;
	} else {
		return false;
	}
}

//imageScale (image Location, new image width, new image height)
//You can choose the new Width, or new Height but not both (if you're going by Height, put a -1 for new Width
//will return an array, element 0 is the new width, element 1 is the new height
function imageScale($image, $newWidth, $newHeight)
{
	if(!$size = @getimagesize($image))
		die("Unable to get info on image $image");
	
	$ratio = ($size[0] / $size[1]);
	
	//scale by height
	if($newWidth == -1)
	{
		$ret[1] = $newHeight;
		$ret[0] = round(($newHeight * $ratio));
	}
	else if($newHeight == -1)
	{
		$ret[0] = $newWidth;
		$ret[1] = round(($newWidth / $ratio));
	}
	else
		die("Scale Error");
	
	return $ret;
}
/* 
 
Created by: Matthew Harris 
   This script was created for anyone to use 
   so I don't really care who uses it or how 
   they use it. 

Purpose: 
   The purpose of this script is that people 
   could rescale their JPEG or PNG images on 
   the fly instead of having to save the image 
   over and over again. 

Things to know: 
 > There is no GIF support in this script right 
   now so dont bother trying :) 
 > Quality can only be adjusted with a JPEG. 
   The maximum is 100 so don't go above. 
 > The default quality used is 80. You can change 
   the default on line 32. Keep in mind that quality 
   changes only affect JPEG images. 
 > The scale is just like division. 2 will give you 
   half the size of the image. 

*/ 

function thumb($source, $scale, $quality = 80) 
{ 
	/* Check for the image's exisitance */ 
	if (!file_exists($source)) { 
		echo 'File does not exist!'; 
	} 
	else { 
		$size = getimagesize($source); // Get the image dimensions and mime type 
		$w = $size[0] / $scale; // Width divided 
		$h = $size[1] / $scale; // Height divided 
		$resize = imagecreatetruecolor($w, $h); // Create a blank image 

		/* Check quality option. If quality is greater than 100, return error */ 
		if ($quality > 100) { 
			echo 'The maximum quality is 100. <br>Quality changes only affect JPEG images.'; 
		} 
		else {             
			header('Content-Type: '.$size['mime']); // Set the mime type for the image 

			switch ($size['mime']) { 
				case 'image/jpeg': 
				$im = imagecreatefromjpeg($source); 
				imagecopyresampled($resize, $im, 0, 0, 0, 0, $w, $h, $size[0], $size[1]); // Resample the original JPEG 
				imagejpeg($resize, '', $quality); // Output the new JPEG 
				break; 

				case 'image/png': 
				$im = imagecreatefrompng($source); 
				imagecopyresampled($resize, $im, 0, 0, 0, 0, $w, $h, $size[0], $size[1]); // Resample the original PNG 
				imagepng($resize, '', $quality); // Output the new PNG 
				break; 
			} 

			imagedestroy($im); 
		} 
	} 
}

function random_image( ) {
	$thumbstring = '';
	$file_dir="pics/a10";	// DIRECTORY WITH THE PICS

	$f_type="tm.jpg";	// FILE EXTENSION YOU WISH TO DISPLAY

	$dir=opendir($file_dir);
	while ($file=readdir($dir))
	{
		if ($file != "." && $file != "..")
		{
			$extension=substr($file,-6);	// THIS DIGIT MUST MATCH THE NUMBER OF CHARACTERS SPECIFIED IN THE FILE EXTENSION ABOVE
			if($extension == $f_type)
			{
				$thumbstring .= "$file|";
			}
		}
	}
	srand((double)microtime()*1000000);
	$arry_txt = explode("|" , $thumbstring);
	echo "<img src='".$file_dir."/".$arry_txt[rand(0, sizeof($arry_txt) -1)]."'>";
}
function image_gallery( ) {
   $count = 1;
   $dirstring = "gallery/";
   $mydir = dir('/home/domain/public_html/gallery');

   echo "<table border=0 cellpadding=0 cellspacing=20 style=border-collapse: collapse bordercolor=#111111 width=100%>";
   while(($file = $mydir->read()) !== false) {
      if ($file !== "." && $file !== "..") {
		if ($count == 1) {
			$fstring = substr("$file", 0, -4);
			$display = "$dirstring" . "$file";
			echo "<table border=0 cellpadding=0 cellspacing=20 style=border-collapse: collapse bordercolor=#111111 width=100%><tr><td width=33% align=center><IMG src=" . "$display width=178 height=174><BR>" . $fstring . "</TD>";
			$count++;
		}
		elseif ($count == 2) {
			$fstring = substr("$file", 0, -4);
			$display = "$dirstring" . "$file";
			echo "<td width=33% align=center><IMG src=" . "$display width=178 height=174><BR>" . $fstring . "</TD>";
			$count++;
		}
		elseif ($count == 3) {
			$fstring = substr("$file", 0, -4);
			$display = "$dirstring" . "$file";
			echo "<td width=33% align=center><IMG src=" . "$display width=178 height=174><BR>" . $fstring . "</TD></tr>";
			$count = 1;
		}
			
      }
   }
   echo "</table>";
   $mydir->close();
}
function cal_points( $year, $round_date, $tournament_id, $hole_points, $gross_standing, $net_standing, &$ryder_points, &$champion_points ) {
	$ryder_points = array();
	$champion_points = array();
	if ($tournament_id > 0 && (date('Y') == $year || $year == 0)) {
		$ryder_points['score'] = $hole_points;
		$ryder_points['standing'] = 0;
		$ryder_points['tourney'] = 2;
		
		$g_ch = 0; $g_ry = 0; $n_ch = 0; $n_ry = 0;
		
		$champion_points['standing'] = 0;
		//echo date('m-d', $round_date) . ' ' . $gross_standing. ' ' . $net_standing. ' ';
		switch ($gross_standing) {
		case 1: $g_ry+=2; $g_ch+=4;
		case 2: $g_ry+=3; $g_ch+=2;
		case 3: $g_ch+=2;
		}
		
		//print_r($ryder_points); echo '<br>';
		switch ($net_standing) {
		case 1: $n_ry+=1; $n_ch+=2;
		case 2: $n_ry+=1; $n_ch+=1;
		case 3: $n_ch+=1;
		}
		if ($g_ch > $n_ch) {
			$champion_points['standing'] = $g_ch;
		} else {
			$champion_points['standing'] = $n_ch;
		}
		if ($g_ry > $n_ry) {
			$ryder_points['standing'] = $g_ry;
		} else {
			$ryder_points['standing'] = $n_ry;
		}
		//print_r($ryder_points); echo '<br>';
	}
}
?>
