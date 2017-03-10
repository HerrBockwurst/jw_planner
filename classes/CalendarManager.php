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
}