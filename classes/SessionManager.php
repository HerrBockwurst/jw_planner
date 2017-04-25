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
}