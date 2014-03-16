<?php
require_once "config.php";
require_once "dfwlaogolf.php";


$dfw = new DfwLaoGolf("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
if (isset($_GET['id']) == true)
{
	$dfw->getTournaments($_GET['id']);
}
else
{
	$dfw->getTournaments(-1);
}
$dfw = null;

?>