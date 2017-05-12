<?php
class GroupManager {
	public static function getGroupsByVers($VS = NULL) {
		$Vers = is_null($VS) ? User::getMyself()->VSID : $VS;		
		$MySQL = MySQL::getInstance();		
		$MySQL->where('vsid', $Vers);		
		$MySQL->select('groups');
		return $MySQL->fetchAll();
	}
	
	public static function getGroupsByUser($UID) {		
		$MySQL = MySQL::getInstance();
		$MySQL->where('members', "%{$UID}%", "LIKE");
		$MySQL->select('groups');
		return $MySQL->fetchAll();
	}
	
	public static function isValidGroup($Set, $VS = NULL) {
		$MySQL = MySQL::getInstance();
		$GIDs = array();
		
		
		foreach(self::getGroups($VS) AS $cGroup)
			$GIDs[] = $cGroup['gid'];
		
		foreach($Set AS $cGroup) 
			if(!in_array($cGroup, $GIDs)) return FALSE;
		
		return TRUE;
		
	}
	
	public static function unsetUser($UID, $Group = NULL) {
		$MySQL = MySQL::getInstance();
		$User = new User($UID);
		
		if(!$User->Valid) return;
		
		if(!is_null($Group)) $MySQL->where('gid', $Group);
		$MySQL->where('vsid', $User->VSID);
		$MySQL->where('members', "%\"{$User->UID}\"%", "LIKE");
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
	
	public static function getUsers($GID) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('gid', $GID);
		$MySQL->select('groups', array('members'), 1);
		
		if($MySQL->countResult() == 0) return array();
		
		return json_decode($MySQL->fetchRow()->members);
	}
}