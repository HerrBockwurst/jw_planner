<?php
class VersManager {
	public static function getVers($IDs = NULL) {
		$MySQL = MySQL::getInstance();
		
		if(!is_null($IDs)) {
			$IDlist = is_array($IDs) ? $IDs : array($IDs);
			foreach($IDlist AS $CID)
				$MySQL->where('vsid', $CID, '=', 'OR');
		}
		$MySQL->select('versammlungen');
		$VersList = array();
		
		foreach($MySQL->fetchAll() AS $Vers)
			$VersList[$Vers['vsid']] = $Vers['name'];
			
			return $VersList;
	}
	
	public static function getVersMembers($VSID) {
		$mysql = MySQL::getInstance();
		$mysql->where('vsid', $VSID);
		$mysql->select('users');
		return $mysql->fetchAll();
	}
}