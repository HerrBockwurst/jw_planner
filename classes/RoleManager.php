<?php
class RoleManager {
	
	public static function addRole($Name, $VSID) {
		$mysql = MySQL::getInstance();
		if(!$mysql->insert('roles', array(
				'vsid' => $VSID,
				'name' => $Name,
				'entry' => "[]"
		))) returnErrorJSON(getString('Errors sql'));
	}
	
	public static function getRoles($Vers = NULL) {
		$vs = $Vers == NULL ? User::getMyself()->VSID : $Vers;
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('vsid', $vs);
		$MySQL->select('roles');
		
		return $MySQL->fetchAll();		
	}
	
	public static function getRole($rid) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('rid', $rid);
		$MySQL->select('roles', NULL, 1);
		return $MySQL->fetchRow();
	}
	
	public static function getFilteredPerms($RID, $Filter) {
		$MySQL = MySQL::getInstance();
		
		if(!is_array($Filter)) $Filter = array($Filter);
		
		$MySQL->where('rid', $RID);
		$MySQL->select('roles', array('entry'), 1);
		
		if($MySQL->countResult() == 0) return array();
		
		$Perms = json_decode($MySQL->fetchRow()->entry);
		return array_diff($Perms, $Filter);
	}
	
	public static function deleteRole($RID) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('rid', $RID);
		if(!$MySQL->delete('roles')) returnErrorJSON(getString('Errors sql'));
		
		$MySQL->where('role', $RID);
		if(!$MySQL->update('users', array('role' => 0))) returnErrorJSON(getString('Errors sql'));
	}
	
	public static function togglePermission($Perms, $RID) {
		$Perms = is_array($Perms) ? $Perms : array($Perms);
		$Role = self::getRole($RID);
		if(!$Role) returnErrorJSON(getString('Errors invalidInput'));
		$CurrentPerms = json_decode($Role->entry);
		$NewPerms = array();
		
		foreach($CurrentPerms AS $cPerm)
			if(!in_array($cPerm, $Perms)) $NewPerms[] = $cPerm; //Alle unbetroffenen Permissions übertragen
		
		foreach($Perms AS $cPerm) 
			if(!in_array($cPerm, $CurrentPerms))
				$NewPerms[] = $cPerm;
		
		$mysql = MySQL::getInstance();
		$mysql->where('rid', $RID);
		if(!$mysql->update('roles', array('entry' => json_encode($NewPerms)))) returnErrorJSON(getString('Errors sql'));		
	}
}