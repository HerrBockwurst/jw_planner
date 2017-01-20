<?php
class User {
	
	private $mysql;
	public $UID, $Clearname, $Mail, $IsLoggedIn = false;
	
	private function __construct() {
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new User();
			return $Instance;
	}
	
	public function Auth() {
		if(!$this->isAuth()) return false;
		
		$this->loadUserData();
	}
	
	private function loadUserData() {
		$this->IsLoggedIn = true;
	}
	
	private function isAuth(): bool {
		$MySQL = MySQL::getInstance();
		//Alte Session löschen
		$MySQL->where('expire', time(), '<');
		$MySQL->delete('sessions');
	
		//Session abfragen
		$MySQL->where('sid', session_id());
		return $MySQL->count('sessions') > 0 ? true : false;
	}
}