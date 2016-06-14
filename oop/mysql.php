<?php
class MySQL {
	private $mysql;
	private $HOST;
	private $USER;
	private $PASSWORD;
	private $DATABASE;
	
	function __construct() {
		$this->HOST = $MYSQL_HOST;
		$this->USER = $MYSQL_USER;
		$this->PASSWORD = $MYSQL_PASSWORD;
		$this->DATABASE = $MYSQL_DATABASE;
		echo $this->USER;
		$this->mysql = new mysqli($this->HOST, $this->USER, $this->PASSWORD, $this->DATABASE);
		if ($this->mysql->connect_errno) {
		    printf("Connect failed: %s\n", $this->mysql->connect_error);
		    exit();
		}
	}
	
	function __destruct() {
		$this->mysql->close();
	}
	
	function doQuery($qry, $return = false) {
		if($return):
			$result = $this->mysql->query($qry);// or die("MySQL-Error: ".$this->mysql->error);
			//$aresult = $result->fetch_all();
			//$result->close();
			return $result;
		else:
			return($this->mysql->query($qry));
		endif;
		
	}
}

$mysql = new MySQL();

?>