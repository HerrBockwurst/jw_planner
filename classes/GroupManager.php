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
	
	public static function unsetUser($UID, $Group = NULL) {
		$MySQL = MySQL::getInstance();
		$User = new Foreigner($UID);
		
		if(!$User->Valid) return;
		
		if(!is_null($Group)) $MySQL->where('gid', $Group);
		$MySQL->where('vsid', $User->VSID);
		$MySQL->select('groups');
		
		foreach($MySQL->fetchAll() AS $cGroup) {
			$Members = json_decode($cGroup['members']);
			if(array_search($User->UID, $Members) !== FALSE)
				unset($Members[array_search($User->UID, $Members)]);
			$Members = json_encode(array_values($Members));
			
			$MySQL->where('gid', $cGroup['gid']);
			if(!$MySQL->update('groups', array('members' => $Members)));
		}
	}
	
	public static function addUser($UID, $Groups) {
		$ToAdd = is_string($Groups) ? array($Groups) : $Groups;
		$MySQL = MySQL::getInstance();
		
		foreach($ToAdd AS $cGroup) {
			$MySQL->where('gid', $cGroup);
			$MySQL->select('groups', array('members'), 1);
			$Members = json_decode($MySQL->fetchRow()->members);
			
			if(array_search($UID, $Members) !== FALSE) continue;
			
			$Members[] = $UID;
			$Members = json_encode(array_values($Members));
			
			$MySQL->where('gid', $cGroup);
			if(!$MySQL->update('groups', array('members' => $Members)));
		}
	}
}