<?php

try {
	$sql_ = new PDO("mysql:host=db473764638.db.1and1.com;dbname=db473764638", "dbo473764638", "4yFaqSzP", $driver_options);

}
catch (exception $e) {
	print("Exception occurred: ".$e->getMessage()."\n");
}

?>