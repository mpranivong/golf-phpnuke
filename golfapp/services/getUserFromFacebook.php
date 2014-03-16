<?php
require_once "config.php";
require_once "dfwlaogolf.php";

if (isset($_GET['id']) == false)
{
	echo '{"error":{"text":"Invalid Tournament Id"}}';
}
else
{
	$dfw = new DfwLaoGolf("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dfw->getUserFromFacebook($_GET['id']);
	$dfw = null;
}

?>