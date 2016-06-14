<?php
class MySQL {
	protected $mysql;
	
	function __construct() {
		$this->mysql = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DATABASE);
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