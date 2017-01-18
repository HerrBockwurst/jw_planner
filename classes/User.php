<?php
class User {
	
	private $mysql;
	
	function __construct() {
		$this->mysql = &$GLOBALS['mysql'];
	}
	
	public function Auth() {
		if(!$this->isAuth()) return false;
		
		$this->loadUserData();
	}
	
	private function loadUserData() {
		
	}
	
	private function isAuth(): bool {
		$mysql =& $this->mysql;
		//Alte Session löschen
		$mysql->where('expire', time(), '<');
		$mysql->delete('sessions');
	
		//Session abfragen
		$mysql->where('sid', session_id());
		return $mysql->count('sessions') > 0 ? true : false;
	}
}