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
	
	public static function getCalendarData($CID) {
		$MySQL = MySQL::getInstance();
		
		$MySQL->where('cid', $CID);
		$MySQL->select('calendar', NULL, 1);
		if($MySQL->countResult() == 0) returnErrorJSON(getString('errors formSubmit'));
		return $MySQL->fetchRow();
	}
}