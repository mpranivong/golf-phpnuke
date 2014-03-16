<?php

include('../config.php');
$the_include = "../db";
global $db, $user_prefix;

// wordpress defaults to 'UTC'
date_default_timezone_set(get_option('timezone_string'));

$user_prefix = 'nuke';

switch($dbtype) {

	case 'MySQL':
		include("".$the_include."/mysql.php");
		break;

	case 'mysql4':
		include("".$the_include."/mysql4.php");
		break;

	case 'sqlite':
		include("".$the_include."/sqlite.php");
		break;

	case 'postgres':
		include("".$the_include."/postgres7.php");
		break;

	case 'mssql':
		include("".$the_include."/mssql.php");
		break;

	case 'oracle':
		include("".$the_include."/oracle.php");
		break;

	case 'msaccess':
		include("".$the_include."/msaccess.php");
		break;

	case 'mssql-odbc':
		include("".$the_include."/mssql-odbc.php");
		break;
	
	case 'db2':
		include("".$the_include."/db2.php");
		break;

}

$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
if(!$db->db_connect_id) {
    die("<br><br><center><img src=images/logo.gif><br><br><b>There seems to be a problem with the $dbtype server, sorry for the inconvenience.<br><br>We should be back shortly.</center></b>");
}

function mxpdev_get_tournaments( $year ) {
	global $db, $user_prefix;
	
	$tournament_year = $year;
	$sql = 'select tournament_id, tournament_name, UNIX_TIMESTAMP(tournament_date) as tournament_date
	from '.$user_prefix.'_golf_tournaments gt where year(tournament_date) = '.$tournament_year;
	$sql .= ' order by tournament_date';	// oldest first, req by richard s
	
	return $db->sql_query($sql);
}