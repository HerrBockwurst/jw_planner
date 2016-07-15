<?php
if(!isset($fromIndex)) exit;

class MySQL {
	private $mysql;
	
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
		
	function error() {
		return $this->mysql->error;
	}
	
	function execute($qry, $param=NULL, $mixed=NULL) {
		/*
		 * Einfaches Execute ohne Prepared Statement
		 */
		if($param == NULL):
			$result = $this->mysql->query($qry) or die("MySQL-Error: ".$this->mysql->error);
			return $result;
		endif;
		
		/*
		 * Prepared Statement
		 */
		
		if(gettype($param) != 'string') return false; //Pr�ft, ob $param eine String ist
		
		if(strlen($param) > 1 && gettype($mixed) != 'array') return false; //Wenn mehrere Werte in $param �bergeben werden, muss $mixed ein Array sein
		
		$stmt = $this->mysql->prepare($qry);
		
		if($stmt == false):
			echo $this->mysql->error;
			exit;
		endif;
		/*
		 * Binde $mixed an $stmt
		 */
		if(strlen($param) > 1):
			/*
			 * Neuer Array f�r Reflection anlegen, da $param erstes Element von $array sein muss.
			 */
			$array = array();
			$array[] = $param;
			for($i = 0; $i<count($mixed); $i++) $array[] = &$mixed[$i]; //Erstellt Referenz
			$ref = new ReflectionClass('mysqli_stmt');
			$method = $ref->getMethod("bind_param");
			$method->invokeArgs($stmt,$array);
		else:
			$stmt->bind_param($param, $mixed);
		endif;

		$stmt->execute();
		$result = $stmt->get_result(); //Return Result Set, wenn vorhanden
		/*
		 * Check Result -> Wenn kein Object dann pr�ft er auf Fehler und gibt true oder false zur�ck
		 */
		
		if(gettype($result) != 'object'):
			if($stmt->errno == 0) $result = true;
			else $result = false;
		endif;
		$stmt->close();
		
		return $result;
	}
}

$mysql = new MySQL();

?>