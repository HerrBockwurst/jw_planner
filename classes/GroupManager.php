<?php
class GroupManager {
	public static function getGroups($VS = NULL) {
		$OwnVS = User::getInstance()->VSID; 
		$GArray = array();
		$VSArray = array();
		$MySQL = MySQL::getInstance();
		
		if(!is_null($VS)) $VSArray = is_array($VS) ? $VS : array($VS);
		else $VSArray = array(User::getInstance()->VSID);
		
		foreach($VSArray AS $cVS) 
			$MySQL->where('vsid', $cVS, '=', 'OR');
		
		$MySQL->select('groups');
		return $MySQL->fetchAll();
	}
}