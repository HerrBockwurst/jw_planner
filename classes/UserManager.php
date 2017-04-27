<?php
class UserManager {
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
}