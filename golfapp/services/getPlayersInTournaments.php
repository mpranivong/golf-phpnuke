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
	if (isset($_GET['uid']) == true)
	{
		$dfw->getPlayersInTournament($_GET['id'], $_GET['uid']);
	}
	else
	{
		$dfw->getPlayersinTournament($_GET['id'], -1);
	}

	$dfw = null;
}

?>