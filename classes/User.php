<?php
class User {
	public $UID, $Name, $VSID, $VSName, $RID, $RoleName, $Active, $IsLoggedIn = FALSE;
	private $Permissions = array();
	
	public function __construct($UID = NULL) {		
		$UID = is_null($UID) ? $this->getUserBySession() : $UID;
		
		if(!$UID) return; //Leeres User Objekt bleibt zurück -> Es existiert keine Session zum Benutzer
		$this->UID = $UID;
		
		$this->getUserData();
	}
	
	public function hasPermission() {
		return FALSE;
	}
	
	private function getUserData() {
		$mysql = MySQL::getInstance();
		
		$mysql->where('uid', $this->UID);
		$mysql->join('users', 'role', 'roles', 'rid', 'LEFT');
		$mysql->join('users', 'vsid', 'versammlungen', 'vsid', 'LEFT');
		$mysql->select('users', array('*', 'role_name' => 'roles.name', 'vs_name' => 'versammlungen.name'), 1);
		
		if($mysql->countResult() == 0) {
			LogManager::Log('UID nicht gefunden.', 'User::gUsrDat');
			return;
		}
		
		$Data = $mysql->fetchRow();
	}
	
	private function getUserBySession() {
		//Alte Sessions löschen
		SessionManager::removeExpiredSessions();
		
		//UID aus Sessions holen
		$Session = SessionManager::getSessionBySID();
		
		if(!$Session) return FALSE;
		return $Session->UID;

	}
	
	public static function getMyself() {
		static $MySelf = NULL;
		if($MySelf === NULL)
			$MySelf = new User();
		return $MySelf;
	}
}