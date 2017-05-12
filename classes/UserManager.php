<?php
class UserManager {
	
	private static $ReservedUsernames = array('system', 'all');
	
	public static function getUserBy($Filter) {
		$mysql = MySQL::getInstance();
		foreach($Filter AS $Col => $cFilter) {
			switch(substr($cFilter, 0, 1)) {
				case '!':
					$mysql->where($Col, substr($cFilter, 1), "!=");
					break;
				case '>':
					$mysql->where($Col, substr($cFilter, 1), ">");
					break;
				case '<':
					$mysql->where($Col, substr($cFilter, 1), "<");
					break;
				case '~':
					$mysql->where($Col, "%".substr($cFilter, 1)."%", "LIKE");
					break;
				default:
					$mysql->where($Col, $cFilter);
					break;				
			}
			$mysql->orderBy('uid');
			$mysql->select('users', array('uid'));
			$RetVal = array();
			foreach($mysql->fetchAll() AS $cUser) 
				$RetVal[] = new User($cUser['uid']);
			return $RetVal;
		}
	}
	
	public static function editUser($UID, $NewData) {
		$mysql = MySQL::getInstance();
		$mysql->where('uid', $UID);
		if(!$mysql->update('users', $NewData)) returnErrorJSON(getString('Errors sql'));
		return TRUE;
	}
	
	public static function addUser($Name, $Password, $Email, $Active, $Vers, $Role, $Groups, $Perms) {
		$mysql = MySQL::getInstance();
		$Username = parseUsername($Name, self::$ReservedUsernames);
		
		if(!$mysql->insert('users', array(
				'uid' => $Username,
				'name' => $Name,
				'password' => password_hash($Password, PASSWORD_DEFAULT),
				'email' => $Email,
				'active' => $Active,
				'vsid' => $Vers,
				'role' => $Role,
				'perms' => json_encode($Perms)
		))) returnErrorJSON(getString('Errors sql'));
		
		if(!empty($Groups)) GroupManager::addUser($Username, $Groups);
		
		return TRUE;
	}
}