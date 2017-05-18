<?php
class CalendarManager {
	public static function getCalendars($VSID = NULL) {
		$VSID = is_null($VSID) ? User::getMyself()->VSID : $VSID;
		$mysql = MySQL::getInstance();
		$mysql->where('vsid', $VSID);
		$mysql->select('calendar');
		return $mysql->fetchAll();		
	}
	
	public static function getCalendar($CID) {
		$mysql = MySQL::getInstance();
		$mysql->where('cid', $CID);
		$mysql->select('calendar', NULL, 1);
		return $mysql->fetchRow();
	}
	
	public static function getPattern($CID) {
		
	}
}