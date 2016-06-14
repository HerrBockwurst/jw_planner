<?php
class MySQL {
	protected $mysql;
	private $MYSQL_HOST = $MYSQL_HOST;
	private $MYSQL_USER = $MYSQL_USER;
	private $MYSQL_PASSWORD = $MYSQL_PASSWORD;
	private $MYSQL_DATABASE = $MYSQL_DATABASE;
	
	function __construct() {
		$this->mysql = new mysqli($this->MYSQL_HOST, $this->$MYSQL_USER, $this->$MYSQL_PASSWORD, $this->$MYSQL_DATABASE);
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