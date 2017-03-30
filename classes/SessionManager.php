<?php
class SessionManager {
	public static function removeExpiredSessions() {
		$mysql = MySQL::getInstance();
		$mysql->where('expire', time(), '<');
		if(!$mysql->delete('sessions')) LogManager::Log('Fehler beim löschen der Sessions.|'.MySQL::getInstance()->Error, 'SM::remExpiredSess', LOG_TYPE_ERR);
	}
	
	public static function getSessionBySID($SID = NULL) {
		if(is_null($SID)) $SID = session_id();
		$mysql = MySQL::getInstance();
		
		$mysql->where('sid', $SID);
		$mysql->select('sessions', NULL, 1);
		
		//Keine Session Vorhanden?
		if($mysql->countResult() == 0) return FALSE;
			
		return $mysql->fetchRow();
	}
}