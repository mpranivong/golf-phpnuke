<?php
require_once "golfapp/services/config.php";
require_once "golfapp/services/dfwlaogolf.php";

$dfw = new DfwLaoGolf("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$dfw->kwe1();
$dfw->kwe2();
$dfw->kwe3();
$dfw = null;

?>