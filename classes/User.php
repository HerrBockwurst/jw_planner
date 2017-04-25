<?php
class User {
	public $UID, $Name, $RID, $RoleName, $VSID, $VSName, $Email, $Password, $Active, $Valid = FALSE;
	private $Permissions = array();
	
	public function __construct($UID = NULL) {
		
		$mysql = MySQL::getInstance();
		$Field = strpos($UID, '@') !== FALSE ? 'email' : 'uid';
		
		$mysql->where($Field, "%{$UID}%", 'LIKE');
		$mysql->join('users', 'role', 'roles', 'rid', 'LEFT');
		$mysql->join('users', 'vsid', 'versammlungen', 'vsid', 'LEFT');
		$mysql->select('users', array('*', 'role_name' => 'roles.name', 'role_perms' => 'roles.entry', 'vers_name' => 'versammlungen.name'), 1);
		
		if($mysql->countResult() == 0) return; //Kein Benutzer gefunden
		$Data = $mysql->fetchRow();
		
		$this->UID = $Data->uid;
		$this->Name = $Data->name;
		$this->RID = $Data->role;
		$this->RoleName = $Data->role_name;
		$this->VSID = $Data->vsid;
		$this->VSName = $Data->vers_name;
		$this->Email = $Data->email;
		$this->Password = $Data->password;
		$this->Active = $Data->active == 1? TRUE : FALSE;
		$this->Valid = TRUE;
		
		$this->Permissions = json_decode($Data->role_perms);
		foreach(json_decode($Data->perms) AS $cPerm)
			if(!in_array($cPerm, $this->Permissions))
					$this->Permissions[] = $cPerm;
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
	
	public function hasVSAccess($VSID) {
		if(array_key_exists($VSID, $this->getAccessableVers())) return TRUE;
		return FALSE;
	}
	
	public static function getMyself(): User {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new User();
		return $Instance;		
	}
}