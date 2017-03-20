<?php
class MessageManager {
	private function __construct() {
	}
	
	public static function getMessageBy($Filter, $Order = array(), $Limit = NULL) {
		$MySQL = MySQL::getInstance();
		foreach($Filter AS $Row => $Value)
			$MySQL->where($Row, $Value);
		foreach($Order AS $Row => $Sort)
			$MySQL->orderBy($Row, $Sort);
		$MySQL->select('messages', NULL, $Limit);
		
		if($MySQL->countResult() <= 1) return $MySQL->fetchRow();
		
		return $MySQL->fetchAll();
	}
	
	public static function getDashboardMessages() {
		$MySQL = MySQL::getInstance();
		$MySQL->where('recipient', 'all');
		$MySQL->where('vsid', User::getInstance()->VSID, '=', 'AND', 'users');
		$MySQL->join('messages', 'sender', 'users', 'uid');
		$MySQL->orderBy('created', 'DESC');
		$MySQL->select('messages', array('*', 'users.name'), 50);
		
		return $MySQL->fetchAll();
	}
}