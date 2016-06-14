<?php
class MySQL {
	protected $mysql;
	
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
	
	function doQuery($qry, $return = false) {
		if($return):
			echo $qry;			
			$result = $this->mysql->query($qry);// or die("MySQL-Error: ".$this->mysql->error);
			echo $this->mysql->errno;
			var_dump($result);
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