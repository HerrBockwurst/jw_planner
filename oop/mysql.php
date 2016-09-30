<?php
class MySQL {
	private $mysql = null, $join = array(), $where = array(), $prep = '', $result = null;
	public $error = array();

	function __construct($host, $user, $password, $db, $port = 3306) {
		$this->mysql = new mysqli($host, $user, $password, $db, $port);
		if ($this->mysql->connect_errno) {
			printf("Connect failed: %s\n", $this->mysql->connect_error);
			exit();
		}
	}

	function __destruct() {
		$this->mysql->close();
	}
	
	public function where($field, $condition, $operator = '=', $addtype = 'AND') {
		$this->where[] = array($field, $condition, $operator, $addtype);
	}
	
	public function join($array) {
		/*
		 * Aufbau Join
		 * array($table1 = field1, $table2 = field2, $type = 'INNER');
		 */
		
		if(!isset($array[2])) $array[2] = "INNER";
		
		$this->join[] = $array;
	}
	
	public function countResult(): int {
		if(gettype($this->result) != 'object') return -1;
		return $this->result->num_rows;
	}
	
	public function fetchRow($asArray = false) {		
		if(gettype($this->result) != 'object') return false;
		if($asArray) $retval = $this->result->fetch_assoc();
		else $retval =  $this->result->fetch_object();
		
		if($retval == null) {
			$this->result->free();			
			$this->result = null;
			return false;
		}
		else return $retval;
	}
	
	public function fetchAll() {
		if(gettype($this->result) != 'object') return false;
		$retval = $this->result->fetch_all(MYSQLI_ASSOC);
		$this->result->free();
		$this->result = null;
		return $retval;
	}
	
	private function createWhereArray(): array {
		if(empty($this->where)) return array(array(),"");
		$ret = ' WHERE ';
		$values = array();
		$i = 0;
		while($where = current($this->where)) {
			if($i == 0)
				$ret .= "`".$where[0]."` ".$where[2]." "."? ";
			else $ret .= $where[3]." `".$where[0]."` ".$where[2]." ? ";
			
			if(is_int($where[1])) $this->prep .= "i";
			elseif(is_numeric($where[1])) $this->prep .= "d";
			else $this->prep .= "s";
			
			$values[] = $where[1];
			
			$i++;
			next($this->where);
		}
		$this->where = array();
		reset($this->where);
		
		return array($values, $ret);
		
	}
	
	private function createJoinString(): string {
		if(empty($this->where)) return "";
		$ret = '';
		foreach($this->join AS $join) {
			$type; $tableA; $fieldA; $tableB; $fieldB;
			$i = 0;
			while($val = current($join)) {
				if($i == 0) {
					$tableA = key($join);
					$fieldA = $val;
				} elseif($i == 1) {
					$tableB = key($join);
					$fieldB = $val;
				} else {
					$type = $val;
				}
				$i++;
				next($join);
			}
			
			$ret .= "$type JOIN `$tableB` ON (`$tableA`.`$fieldA` = `$tableB`.`$fieldB`) ";
		}
		$this->join = array();
		reset($this->join);
		return $ret;
	}
	
	public function insert($table, $fields): bool {
		$this->prep = '';
		$qry = "INSERT INTO `$table` (";
		$values = array();
		$locPreps = '';
	
		foreach($fields AS $field => $value) {
			$qry .= "`".$field."`, ";
			$values[] = $value;
				
			if(is_int($value)) $locPreps .= "i";
			elseif(is_numeric($value)) $locPreps .= "d";
			else $locPreps .= "s";
		}
		
		$qry = rtrim($qry, ', ');
		$qry .= ") VALUES (";
		
		for($i = 1; $i <= count($values); $i++) $qry .= "?, ";
		$qry = rtrim($qry, ', ');
		$qry .= ")";
		
		$stmt = $this->mysql->prepare($qry);
		if(!$stmt) {
			$this->error = array("Fehler beim Erstellen des Statements: ".$this->mysql->error, $qry);
			return false;
		}		
		
		$toInvoke = array($locPreps);
		while(current($values) !== false) {
			$toInvoke[] = &$values[key($values)]; //Referenz für Update Werte
			next($values);
		}
		
		$ref = new ReflectionClass('mysqli_stmt');
		$method = $ref->getMethod("bind_param");
		$method->invokeArgs($stmt,$toInvoke);
	
		$stmt->execute();		
		if($stmt->sqlstate == '00000') return true;
		else return false;
	}
	
	public function delete($table): bool {
		$this->prep = '';
		$qry = "DELETE FROM `$table` ";
		$values = array();
	
		$where = $this->createWhereArray();
		$qry .= $where[1];
	
		$stmt = $this->mysql->prepare($qry);
		if(!$stmt) {
			$this->error = array("Fehler beim Erstellen des Statements: ".$this->mysql->error, $qry);
			return false;
		}
		
		if($this->prep != '') {
			$toInvoke = array($this->prep);				
				
			while(current($where[0]) !== false) {
				$toInvoke[] = &$where[0][key($where[0])]; //WICHTIG: Referenz bilden
				next($where[0]);
			}
				
			$ref = new ReflectionClass('mysqli_stmt');
			$method = $ref->getMethod("bind_param");
			$method->invokeArgs($stmt,$toInvoke);
		}
	
		$stmt->execute();
		if($stmt->sqlstate == '00000') return true;
		else return false;
	}
	
	public function update($table, $fields): bool {
		$this->prep = '';
		$qry = "UPDATE `$table` SET ";
		$values = array();
		$locPreps = '';
		
		foreach($fields AS $field => $value) {
			$qry .= "`$field` = ?, ";
			$values[] = $value;
			
			if(is_int($value)) $locPreps .= "i";
			elseif(is_numeric($value)) $locPreps .= "d";
			else $locPreps .= "s";
		}		
		$qry = rtrim($qry, ', ');
		
		$where = $this->createWhereArray();
		$locPreps .= $this->prep;
		$qry .= $where[1];
		
		$stmt = $this->mysql->prepare($qry);
		if(!$stmt) {
			$this->error = array("Fehler beim Erstellen des Statements: ".$this->mysql->error, $qry);
			return false;			
		}
		
		$toInvoke = array($locPreps);
		while(current($values) !== false) {
			$toInvoke[] = &$values[key($values)]; //Referenz für Update Werte
			next($values);
		}
		
		while(current($where[0]) !== false) {				
			$toInvoke[] = &$where[0][key($where[0])]; //Referenz für Where Werte
			next($where[0]);
		}
		
		$ref = new ReflectionClass('mysqli_stmt');
		$method = $ref->getMethod("bind_param");
		$method->invokeArgs($stmt,$toInvoke);
		
		$stmt->execute();
		if($stmt->sqlstate == '00000') return true; 
		else return false;		
	}
	
	public function select($table, $fields = null, $limit = null) {
		$this->prep = '';
		if($fields != null) {
			$fieldstring = '';
				
			while($field = current($fields)) {
				$pre = '';
				if(strpos($field, ".") === false) $pre = $table;
				if(is_int(key($fields))) $fieldstring .= "`".$pre."`.`".$field."`, ";
				else $fieldstring .= "`".$pre."`.`".key($fields)."` AS ".$field.", ";
			
				next($fields);
			}
				
			$fieldstring = rtrim($fieldstring, ', ');
		} else { $fieldstring = '*'; }
		
		$where = $this->createWhereArray();
		$join = $this->createJoinString();
		$limitstring = '';
		if($limit != null) $limitstring = " LIMIT ".$limit;
		
		$qry = "SELECT $fieldstring FROM `$table` $join ".$where[1].$limitstring;
		
		$stmt = $this->mysql->prepare($qry);
		if(!$stmt) {
			$this->error = array("Fehler beim Erstellen des Statements: ".$this->mysql->error, $qry);
			return false;
		}
		
		if($this->prep != '') {
			$toInvoke = array($this->prep);
			
			while(current($where[0])) {				
				$toInvoke[] = &$where[0][key($where[0])]; //WICHTIG: Referenz bilden
				next($where[0]);
			}
			
			$ref = new ReflectionClass('mysqli_stmt');
			$method = $ref->getMethod("bind_param");
			$method->invokeArgs($stmt,$toInvoke);
		}
		
		$stmt->execute();
		$this->result = $stmt->get_result();
		return true;
	}
	
	public function query($qry, $data = array()) {
		$this->prep = '';
		$locPreps = '';		
		
		foreach($data AS $value) {
			if(is_int($value)) $locPreps .= "i";
			elseif(is_numeric($value)) $locPreps .= "d";
			else $locPreps .= "s";
		}
		
		$stmt = $this->mysql->prepare($qry);
		if(!$stmt) {
			$this->error = array("Fehler beim Erstellen des Statements: ".$this->mysql->error, $qry);
			return false;
		}
		
		if($locPreps != '') {
			
			$toInvoke = array($locPreps);
			foreach($data AS $value) {
				$toInvoke[] = $value;
			}
			
			$ref = new ReflectionClass('mysqli_stmt');
			$method = $ref->getMethod("bind_param");
			$method->invokeArgs($stmt,$toInvoke);
		}
		
		$stmt->execute();
		$this->result = $stmt->get_result();
		return true;
	}
}

$mysql = new MySQL(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWD, MYSQL_DB);