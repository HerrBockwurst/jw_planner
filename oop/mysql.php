<?php
class MySQL {
	private $mysql;
	private $result;
	
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
	
	function free() {
		$this->result->free();
	}
	
	function error() {
		return $this->mysql->error;
	}
	
	function query($qry, $return = false) {
		if($return):
			$this->result = $this->mysql->query($qry) or die("MySQL-Error: ".$this->mysql->error);
			if($this->result->num_rows == 1):
				$aresult = $this->result->fetch_assoc();
				$this->free();
				return $aresult;
			else:
				return $this->result;
			endif;
		else:
			return($this->mysql->query($qry));
		endif;
		
	}
}

$mysql = new MySQL();

?>