<?php
try {
	// mysql_connect("localhost","u40432415","ho387vns");
	mysql_connect("db473764638.db.1and1.com","dbo473764638","4yFaqSzP");	
	mysql_select_db("db473764638");
	
	$q=mysql_query("SELECT * FROM nuke_golf_players limit 10");
	while($e=mysql_fetch_assoc($q))
	{
		$output[]=$e;
	}
	
	print(json_encode($output));
}
catch (exception $e) {
	print("Exception occurred: ".$e->getMessage()."\n");
}

mysql_close();

?>