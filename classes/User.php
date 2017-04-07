<?php
class User {
	public $UID, $Name, $VSID, $VSName, $RID, $RoleName, $Active, $IsLoggedIn = FALSE;
	public $Permissions = array();
	
	public function __construct($UID = NULL) {		
		$UID = is_null($UID) ? $this->getUserBySession() : $UID;
		
		if(!$UID) return; //Leeres User Objekt bleibt zurück -> Es existiert keine Session zum Benutzer		
		$this->UID = $UID;
		
		$this->getUserData();		
	}
	
	public function hasPermission($Perm) {
		if(is_null($Perm)) return TRUE;
		if(in_array($Perm, $this->Permissions)) return TRUE;
		return FALSE;
	}
	
	public function getAccessableVers() {
		$VS = $this->searchPerm('access.vs.');
		if(empty($VS)) return array($this->VSID => $this->VSName);
		if(array_search('*', $VS) !== FALSE) return VersManager::getVers();
		return VersManager::getVers($VS);
	}
	
	public function searchPerm($Needle, $FullPerm = FALSE) {
		$SelectedPerms = array();
		foreach($this->Permissions AS $Perm) {
			if(strpos($Perm, $Needle) === FALSE) continue;
			$SelectedPerms[] = $FullPerm ? $Perm : substr($Perm, strlen($Needle) + strpos($Perm, $Needle));
		}
		return $SelectedPerms;
	}
	
	private function getUserPerms() {
		$mysql = MySQL::getInstance();
		
		$mysql->where('uid', $this->UID);
		$mysql->join('users', 'role', 'roles', 'rid', 'LEFT');
		$mysql->select('users', array('perms', 'roles.entry'), 1);
		
		$Obj = $mysql->fetchRow();
		$this->Permissions = json_decode($Obj->entry);
		
		foreach(json_decode($Obj->perms) AS $SpecialPerm)
			$this->Permissions[] = $SpecialPerm;
		
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
		$this->Name = $Data->name;
		$this->VSID = $Data->vsid;
		$this->VSName = $Data->vs_name;
		$this->RID = $Data->role;
		$this->RoleName = $Data->role_name;
		$this->Active = $Data->active;
		
		$this->getUserPerms();
	}
	
	private function getUserBySession() {
		//Alte Sessions löschen
		SessionManager::removeExpiredSessions();
		
		//UID aus Sessions holen
		$Session = SessionManager::getSessionBySID();
		
		if(!$Session) return FALSE;
		
		$this->IsLoggedIn = TRUE;
		return $Session->uid;

	}
	
	public static function getMyself() {
		static $MySelf = NULL;
		if($MySelf === NULL)
			$MySelf = new User();
		return $MySelf;
	}
}