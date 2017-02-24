<?php
class Foreigner {
	public $Valid, $UID, $Clearname, $Mail, $VSID, $Vers, $RoleID, $Active;
	
	public function  __construct($uid) {
		$this->Valid = FALSE;
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('uid', $uid);
		$MySQL->join('users', 'vsid', 'versammlungen', 'vsid');
		$MySQL->select('users', array('*', 'vs_name' => 'versammlungen.name'), 1);
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
		
		$this->Valid = TRUE;
	}
	
}