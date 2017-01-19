<?php
class User {
	
	private $mysql;
	public $UID, $Clearname, $Mail, $IsLoggedIn = false;
	
	function __construct() { 
	}
	
	public function Auth() {
		if(!$this->isAuth()) return false;
		
		$this->loadUserData();
	}
	
	private function loadUserData() {		
	}
	
	private function isAuth(): bool {
		global $MySQL;
		//Alte Session löschen
		$MySQL->where('expire', time(), '<');
		$MySQL->delete('sessions');
	
		//Session abfragen
		$MySQL->where('sid', session_id());
		return $MySQL->count('sessions') > 0 ? true : false;
	}
}