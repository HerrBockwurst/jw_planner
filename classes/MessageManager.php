<?php
class MessageManager {
	private function __construct() {
	}
	
	public static function sendMessage($Recipient, $Title, $Text, $Expire = PHP_INT_MAX, $Sender = NULL) {
		if(is_null($Sender)) $Sender =  User::getInstance()->UID;
		$MySQL = MySQL::getInstance();
		
		if(!$MySQL->insert('messages', array(
				'title' => $Title,
				'content' => $Text,
				'created' => time(),
				'sender' => $Sender,
				'expire' => $Expire,
				'recipient' => $Recipient
		))) returnErrorJSON(getString('errors sql'));
	}
	
	public static function delMessage($MID) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('msg_id', $MID);
		if(!$MySQL->delete('messages')) returnErrorJSON(getString('errors sql'));
	}
	
	public static function getMessage($MID) {
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('msg_id', $MID);
		$MySQL->select('messages', NULL, 1);
		return $MySQL->fetchRow();
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
		$MySQL->where('expire', time(), ">=");
		$MySQL->where('vsid', User::getInstance()->VSID, '=', 'AND', 'users');
		$MySQL->join('messages', 'sender', 'users', 'uid');
		$MySQL->orderBy('created', 'DESC');
		$MySQL->select('messages', array('*', 'users.name'), 50);
		
		return $MySQL->fetchAll();
	}
}