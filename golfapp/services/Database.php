<?php

class Database
{
	public $pdo_;

	public function __construct($dsn, $username, $password)
	{
		$this->pdo_ = new PDO($dsn, $username, $password);
	}
	
	public function query($sql, &$error, $className="stdClass", $ctorArgs=array())
	{
		$object = null;
		try {
			$stmt = $this->pdo_->query($sql);
			if ($stmt == false)
			{
				$error = "SQL Error: ".print_r($stmt->errorInfo(),true).", Query = $sql";
			}
			else
			{
				$object = $stmt->fetchAll(PDO::FETCH_CLASS, $className, $ctorArgs);
			}
		}
		catch(PDOException $e) {
			$error = $e->getMessage();
		}
	
		return $object;
	}
	
// 	public function pquery($q /* , ... */)
// 	{
// 		$args = func_get_args();
// 		array_shift($args); // drop $q
	
// 		// filter through and handle bools and ByteArrays.
// 		foreach ($args as $k => $v) {
// 			if (is_bool($v)) {
// 				$args[$k] = $v ? 1 : 0;
// 			} else if (is_object($v) && get_class($v) == "ByteArray") {
// 				$args[$k] = $v->data;
// 			}
// 		}
	
// 		return $this->xquery($q, $args);
// 	}	
	
// 	private function xquery($q, $args)
// 	{
// 		$stmt = $this->pdo->prepare($q);
// 		$ok = $stmt->execute($args);
// 		if (!$ok) {
// 			$this->num_rows_affected = 0;
// 			throw new LxtSqlError($this->pdo, $q, $args);
// 		}
// 		$this->num_rows_affected = $stmt->rowCount();
// 		return new LxtSqlResults($stmt);
//		return $stmt->fetchAll(PDO::FETCH_CLASS, $className, $ctorArgs);
// 	}
};

?>
