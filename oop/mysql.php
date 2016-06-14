<?php
class MySQL {
	private $mysql;
	
	
	function __construct() {
		global $MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DATABASE;
		$this->mysql = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DATABASE);
		if ($this->mysql->connect_errno) {
		    printf("Connect failed: %s\n", $this->mysql->connect_error);
		    exit();
		}
	}
	
	function __destruct() {
		$this->mysql->close();
	}
	
	function query($qry, $return = false) {
		if($return):
			$result = $this->mysql->query($qry) or die("MySQL-Error: ".$this->mysql->error);
			echo $result->num_rows;
			$aresult = $result->fetch_assoc();
			var_dump($aresult);
			$result->free();
			return $aresult;
		else:
			return($this->mysql->query($qry));
		endif;
		
	}
}

$mysql = new MySQL();

?>