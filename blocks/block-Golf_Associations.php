<?php

########################################################################
# PHP-Nuke Block: Golf Members                                         #
#                                                                      #
# Copyright (c) 2005 by M. Pranivong (tournaments@dfwlga.com)          #
#                                                                      #
# 2005-09-12 Derived from existing modules                             #
########################################################################
# This program is free software. You can redistribute it and/or modify #
# it under the terms of the GNU General Public License as published by #
# the Free Software Foundation; either version 2 of the License.       # 
######################################################################## 

if (eregi("block-Golf_Associations.php", $_SERVER['PHP_SELF'])) {
    Header("Location: index.php");
    die();
}

require_once("./modules/Golf/golf.class.php");

global $user, $prefix, $db, $user_prefix, $member_titles;

//$content = "<marquee behavior='scroll' direction='up' height='80px' scrollamount='1' scrolldelay='10' onMouseOver='this.stop()' onMouseOut='this.start()'>";
$content .= '
<table border="0" cellspacing="0" z-index=15>

<tr><td><a target="_blank" href="http://www.sclaogolf.com/home.htm">SoCal</a></td></tr>
<tr><td><a target="_blank" href="http://www.mtlaogolf.com/">Middle Tennessee</a></td></tr>
<tr><td><a target="_blank" href="http://www.laogolf.com/">Seattle</a></td></tr>
<tr><td><a target="_blank" href="http://www.orlga.com/">Oregon</a></td></tr>
<tr><td><a target="_blank" href="http://www.dfwlta.shutterfly.com/">DFW Lao Tennis</a></td></tr>
</table>';

//$content .= '</marquee>';
?>