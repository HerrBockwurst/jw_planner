<?php
class CalendarManager {
	public static function removeUser ($UID) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('entrys', '%"'.$UID.'"%', 'LIKE');
		$MySQL->select('posts', array('pid', 'entrys'));
		
		foreach($MySQL->fetchAll() AS $cPost) {
			$Entrys = json_decode($cPost['entrys']);
			unset($Entrys[array_search($UID, $Entrys)]);
			$Entrys = json_encode(array_values($Entrys));
			
			$MySQL->where('pid', $cPost['pid']);
			if(!$MySQL->update('posts', array('entrys' => $Entrys))) returnErrorJSON(getString('errors sql'));
		}
	}
	
	public static function getCalendars($VSID) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('vsid', $VSID);
		$MySQL->select('calendar');
		return $MySQL->fetchAll();
	}
	
	public static function getOpenSearches($VSID = NULL, $CID = NULL) {
		if(is_null($VSID) && is_null($CID)) return 0;
		$Searches = 0;
		$MySQL = MySQL::getInstance();
		$Continue = FALSE;
		
		$CIDs = is_null($CID) ? self::getCalendars($VSID) : array(self::getCalendarData($CID, TRUE));
		$CIDsToAdd = array(); //Muss Cids erst zwischenspeichern, da sich sonst 2 SQL Anfragen überschneiden
		
		foreach($CIDs AS $cCalendar) {
			if(!User::getInstance()->hasCalendarAccess($cCalendar['cid'])) continue;
			$CIDsToAdd[] = $cCalendar['cid'];			
			$Continue = TRUE;
		}
		foreach($CIDsToAdd AS $cCID)
			$MySQL->where('cid', $cCID, '=', 'OR');
		
		if(!$Continue) return 0; //Hat keinen Zugriff auf Kalender
		
		$MySQL->where('entrys', '[]', '<>');
		$MySQL->where('req', '[]', '<>');
		$MySQL->select('posts');
		foreach($MySQL->fetchAll() AS $cPost)
			if(count(json_decode($cPost['req'])) > 0 && count(json_decode($cPost['entrys'])) != $cPost['count'] && $cPost['start'] > time())
				$Searches++;
		return $Searches;
	}
	
	public static function cleanUpPosts() {
		$MySQL = MySQL::getInstance();
		$MySQL->where('end', time() - (POST_STORE_TIME *30*24*60*60), '<');
		$MySQL->delete('posts'); //Todo Log
	}
	
	public static function toggleListMode($CID) {
		$MySQL = MySQL::getInstance();
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', array('vsid', 'listmode'), 1);
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit'));
	
		$Calendar = $MySQL->fetchRow();
		User::getInstance()->checkVSAccess($Calendar->vsid);
		
		$NewMode = $Calendar->listmode == "blacklist" ? "whitelist" : "blacklist";
		$MySQL->where('cid', $CID);
		if(!$MySQL->update('calendar', array('listmode' => $NewMode))) returnErrorJSON(getString('errors sql'));
	}
	
	public static function updateLists($Blacklist, $Whitelist, $CID) {
		$MySQL = MySQL::getInstance();
	
		$MySQL->where('cid', $CID);
		if(!$MySQL->update('calendar', array(
				"blacklist" => json_encode($Blacklist),
				"whitelist" => json_encode($Whitelist)
		))) returnErrorJSON(getString('errors sql'));
	}
	
	public static function getCalendarData($CID, $ASSOC = FALSE) {
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', NULL, 1);
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit'));
		return $MySQL->fetchRow($ASSOC);
	}
}