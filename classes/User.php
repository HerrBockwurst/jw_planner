<?php
class User {
	
	private $Permissions = array(); 
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
	
	public function hasPerm($Perm) {
		return in_array($Perm, $this->Permissions);
	}
	
	private function loadUserData() {
		$this->IsLoggedIn = true;
		$MySQL = MySQL::getInstance();
		
		//Daten aus Sessions abrufen
		$MySQL->where('sid', session_id());
		$MySQL->select('sessions', array('uid'));
		$this->UID = $MySQL->fetchRow()->uid;
		
		//Session updaten
		$MySQL->where('sid', session_id());
		$MySQL->update('sessions', array('expire' => time() + (60*SESSIONTIME)));
		
		//Benutzerdaten speichern
		$MySQL->where('uid', $this->UID);
		$MySQL->select('users', NULL, 1);
		$Userdata = $MySQL->fetchRow();
		
		$this->Clearname = $Userdata->name;
		
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