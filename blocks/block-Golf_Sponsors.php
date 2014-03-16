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

if (eregi("block-Golf_Sponsors.php", $_SERVER['PHP_SELF'])) {
    Header("Location: index.php");
    die();
}

require_once("./modules/Golf/golf.class.php");

global $user, $prefix, $db, $user_prefix, $member_titles;

//$content = "<marquee behavior='scroll' direction='up' height='80px' scrollamount='1' scrolldelay='10' onMouseOver='this.stop()' onMouseOut='this.start()'>";
$content .= '
<table border="0" cellspacing="0" z-index=15>
<tr><td><a title="Express Salon" target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=55">Express Salon</a></td></tr>
<tr><td><a title="Star Liquor" target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=56">Star Liquor</a></td></tr>

<!--tr><td><a title="Freight services" target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=54"><img src="modules/Golf/sponsors/texaslandandair150.gif" border="0"/></a></td></tr>
<tr><td><a title="Thai & Japanese" target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=53"><img src="modules/Golf/sponsors/zenna150.gif" border="0"/></a></td></tr>
<tr><td><a title="Aviation staffing services" target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=52"><img src="modules/Golf/sponsors/ais150w.jpg" border="0"/></a></td></tr>
<tr><td><a title="Realestate and loans" target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=24"><img src="modules/Golf/sponsors/lucksoon_150w_fine_prop.jpg" border="0"/></a></td></tr>
<tr><td><br></td></tr>
<tr><td><a target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=20"><img src="modules/Golf/sponsors/chan-sene-bizcard_150x60.JPG" border="0"/></a></td></tr>
<tr><td><br></td></tr>
<tr><td><a target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=22">Taylor Realty</a></td></tr>
<tr><td><br></td></tr>
<tr><td>
	<a target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=23">
	<embed src="modules/Golf/sponsors/VongduaneStudioB-150x60.swf" width="150" height="60"
	type="application/x-shockwave-flash"
	pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
	</embed>
	<a>
</td></tr>
<tr><td><br></td></tr>
<tr><td><a target="_blank" href="modules.php?name=Web_Links&l_op=visit&lid=34"><img src="modules/Golf/sponsors/jerry_srisunakorn.gif" width="150" border="0"></a></td></tr-->
</table>';

//$content .= '</marquee>';
?>