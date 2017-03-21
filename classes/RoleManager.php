<?php
class RoleManager {
	public static function getRoles($Vers = NULL) {
		$vs = $Vers == NULL ? User::getInstance()->VSID : $Vers;
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
		
		foreach($Filter AS $cFilter)
			if(array_search($cFilter, $Perms))
				unset($Perms[array_search($cFilter, $Filter)]);
		
		return $Perms;
	}
}