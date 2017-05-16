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
}