<?php
class MySQL {
	private $mysql;
	
	function __construct() {
		$this->mysql = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DATABASE);
		if ($mysql->connect_errno) {
		    printf("Connect failed: %s\n", $mysql->connect_error);
		    exit();
		}
	}
	
	function __destruct() {
		$this->mysql->close();
	}
	
	function query($qry, $return = false) {
		if($return):
			$result = $this->mysql->query($qry) or die("MySQL-Error: ".$this->mysql->error);
			
			$aresult = $result->fetch_all();
			$result->close();
			return $aresult;
		else:
			return($this->mysql->query($qry));
		endif;
		
	}
}

$mysql = new MySQL();

?>