<?php
class User {
	
	private $Permissions = array(); 
	public $UID, $Clearname, $Mail, $IsLoggedIn = false, $VSID, $Vers, $RoleName, $RoleID;
	
	private function __construct() {
	}
	
	public static function getInstance() : User {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new User();
		return $Instance;
	}
	
	public function hasCalendarAccess($CID) {
		if($this->hasPerm('admin.calendar')) return TRUE; //Überspringe anfrage für Admin
		$Calendar = CalendarManager::getCalendarData($CID);
		
		$Blacklist = json_decode($Calendar->blacklist);
		$Whitelist = json_decode($Calendar->whitelist);
		$Mode = $Calendar->listmode;
		
		if($Mode == "blacklist") {
			foreach($Blacklist AS $cGroup)
				if(in_array($this->UID, GroupManager::getUsers($cGroup))) return FALSE;
				return TRUE;
		} else {
			foreach($Whitelist AS $cGroup)
				if(in_array($this->UID, GroupManager::getUsers($cGroup))) return TRUE;
				return FALSE;
		}
	}
	
	public function Auth() {
		if(!$this->isAuth()) return false;
		
		$this->loadUserData();
	}
	
	public function hasPerm($Perm) {
		return in_array($Perm, $this->Permissions);
	}
	
	public function hasVSAccess($VSID) {
		if(array_key_exists($VSID, $this->getAccessableVers())) return TRUE;
		return FALSE;
	}
	
	public function checkVSAccess($VSID) {
		if(!$this->hasVSAccess($VSID)) returnErrorJSON(getString('errors noPerm')); //Keine Rechte für VS
	}
	
	public function getClearedPerms() {
		$ReturnVal = array();
		foreach($this->Permissions AS $cPerm) {
			if(strpos($cPerm, '.vs.') !== FALSE) continue;
			$ReturnVal[] = $cPerm;
		}
		return $ReturnVal;
	}
	
	public function searchPerm($Needle, $FullPerm = FALSE) {
		$SelectedPerms = array();
		foreach($this->Permissions AS $Perm) {
			if(strpos($Perm, $Needle) === FALSE) continue;
			$SelectedPerms[] = $FullPerm ? $Perm : substr($Perm, strlen($Needle) + strpos($Perm, $Needle));
		}
		return $SelectedPerms;
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
		$MySQL->join('users', 'role', 'roles', 'rid', 'LEFT');
		$MySQL->join('users', 'vsid', 'versammlungen', 'vsid', 'LEFT');
		$MySQL->select('users', array('*', 'role_name' => 'roles.name', 'vs_name' => 'versammlungen.name'), 1);
		$Userdata = $MySQL->fetchRow();
		
		$this->Clearname = $Userdata->name;
		$this->VSID = $Userdata->vsid;
		$this->Permissions = PermissionManager::getPermsByRole($Userdata->role);
		foreach(json_decode($Userdata->perms) AS $Perm)
			if(!in_array($Perm, $this->Permissions))
				$this->Permissions[] = $Perm;
		$this->RoleName = $Userdata->role_name;
		$this->RoleID = $Userdata->role;
		$this->Vers = $Userdata->vs_name;
		//Todo
	}
	
	public function getAccessableVers() {
		$VS = $this->searchPerm('useredit.vs.');
		if(empty($VS)) return array($this->VSID => $this->Vers);
		if(array_search('*', $VS) !== FALSE) return VersManager::getVers();
		return VersManager::getVers($VS);
		
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