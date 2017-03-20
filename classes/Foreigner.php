<?php
class Foreigner {
	public $Valid, $UID, $Clearname, $Mail, $VSID, $Vers, $RoleID, $Active;
	
	private $Permissions;
	
	public function getFilteredPerms($Filter)  {
		$RetVal = array();
		foreach($this->Permissions AS $cPerm)
			if(!in_array($cPerm, $Filter))
				$RetVal[] = $cPerm;
				
		return $RetVal;
	}
	
	public function  __construct($uid) {
		$this->Valid = FALSE;
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('uid', $uid);
		$MySQL->join('users', 'vsid', 'versammlungen', 'vsid');
		$MySQL->select('users', array('*', 'vs_name' => 'versammlungen.name'), 1);
		if($MySQL->countResult() != 1) return FALSE;
		$Userdata = $MySQL->fetchRow();
		
		if($Userdata->vsid != User::getInstance()->VSID && !array_key_exists($Userdata->vsid, User::getInstance()->getAccessableVers())) return FALSE;
		
		$this->UID = $Userdata->uid;
		$this->Clearname = $Userdata->name;
		$this->Mail = $Userdata->email;
		$this->UID = $Userdata->uid;
		$this->VSID = $Userdata->vsid;
		$this->Vers = $Userdata->vs_name;
		$this->RoleID = intval($Userdata->role);
		$this->Active = intval($Userdata->active);
		$this->Permissions = json_decode($Userdata->perms);
		
		$this->Valid = TRUE;
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

	public function hasPerm($Perm) {
		return array_search($Perm, $this->Permissions) !== FALSE ? TRUE : FALSE;
	}
	
	public function updateMe($UpdateData) {
		if(empty($UpdateData)) return TRUE;
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('uid', $this->UID);
		return $MySQL->update('users', $UpdateData);		
	}
	
	public function deleteMe() {
		$MySQL = MySQL::getInstance();
		$MySQL->where('uid', $this->UID);
		if(!$MySQL->delete('users')) returnErrorJSON(getString('errors sql'));
	}
	
}