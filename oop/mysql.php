<?php
class MySQL {
	private $mysql;
	private $result;
	
	function __construct() {
		global $CONFIG;
		$this->mysql = new mysqli($CONFIG['MYSQL_HOST'], $CONFIG['MYSQL_USER'], $CONFIG['MYSQL_PASSWORD'], $CONFIG['MYSQL_DATABASE']);
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
	
	function execute($qry, $param=NULL, $mixed=NULL) {
		/*
		 * Einfaches Execute ohne Prepared Statement
		 */
		if($param == NULL):
			$this->result =  $this->mysql->query($qry) or die("MySQL-Error: ".$this->mysql->error);
			return $this->result;
		endif;
		
		/*
		 * Prepared Statement
		 */
		
		if(gettype($param) != 'string') return false; //Prüft, ob $param eine String ist
		
		if(strlen($param) > 1 && gettype($mixed) != 'array') return false; //Wenn mehrere Werte in $param übergeben werden, muss $mixed ein Array sein
		
		$stmt = $this->mysql->prepare($qry);

		/*
		 * Binde $mixed an $stmt
		 */
		
		if(strlen($param) > 1):
			/*
			 * Neuer Array für Reflection anlegen, da $param erstes Element von $array sein muss.
			 */
			$array = array();
			$array[] = $param;
			foreach($mixed as &$value) $array[] = $value;
			$ref = new ReflectionClass('mysqli_stmt');
			$method = $ref->getMethod("bind_param");
			$method->invokeArgs($stmt,$array);
		else:
			$stmt->bind_param($param, $mixed);
		endif;
		
		$stmt->execute();
		$this->result = $stmt->get_result();
		$stmt->close();
		return $this->result;
	}
}

$mysql = new MySQL();

?>