<?php 
require_once "config.php";
require_once "Database.php";

$db= new Database("mysql:host=$g_dbhost;dbname=$g_dbname", $g_dbuser, $g_dbpass);

$currentyear = date("Y");
$sql = 'select gt.tournament_id, gt.tournament_name, gt.tournament_deadline, '.
		'gt.tournament_date as unformatted_date, '.
		'gt.tournament_format, '.
		'UNIX_TIMESTAMP(gt.tournament_date) as tournament_date, '.
		'gc.course_name, '.
		'gt.course_id '.
		'from nuke_golf_tournaments gt '.
		'left join nuke_golf_courses gc on gc.course_id=gt.course_id ';
$sql .= 'where (UNIX_TIMESTAMP(tournament_date) !=0) and (year(curdate()) = year(tournament_date)) ';
$sql .= ' order by tournament_date desc';

$ret = $db->query($sql, $error);
if ($ret != null)
{
	echo '{"items":'. json_encode($ret) .'}';
}
else
{
	echo '{"error":{"text":'. $error .'}}';
}

?>