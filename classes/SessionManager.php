<?php
class SessionManager {
	static function getBans():int {
		$mysql = MySQL::getInstance();
		$mysql->where('ip', $_SERVER['REMOTE_ADDR']);
		return $mysql->count('loginfails', 'ip');
	}
	
	static function addSession($UID) {
		$mysql = MySQL::getInstance();
		if(!$mysql->insert('sessions', array('sid' => session_id(), 'uid' => $UID, 'expire' => time() + SESSIONTIME)))
			returnErrorJSON(getString('Errors sql'));		
	}
	
	static function getUserBySession() {
		$mysql = MySQL::getInstance();
		$mysql->where('sid', session_id());
		$mysql->select('sessions', array('uid'), 1);
		
		if($mysql->countResult() == 0) return false;
		return $mysql->fetchRow()->uid;
	}
	
	static function removeExpiredSessions() {
		$mysql = MySQL::getInstance();
		$mysql->where('expire', time(), '<');
		if(!$mysql->delete('sessions')) returnErrorJSON(getString('Errors sql'));
	}
}