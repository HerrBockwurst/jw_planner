<?php
class User {
	public $UID, $Name, $VSID, $VSName, $RID, $RoleName, $Active;
	private $Permissions = array();
	
	public function __construct($UID = NULL) {		
				
	}
	
	private function getUserBySession() {
		$mysql = MySQL::getInstance();
		
		//Alte Sessions löschen
		$mysql->where('expire', time(), '<');
		$mysql->delete('sessions');
		
		//UID aus Sessions holen
		$mysql->where('sid', session_id());
		$mysql->select('sessions', NULL, 1);
		
		//Keine UID Vorhanden?
		if($mysql->countResult() == 0) return FALSE;
			
		$Row = $mysql->fetchRow();
		
		
		

	}
	
	public static function getMyself() {
		static $MySelf = NULL;
		if($MySelf === NULL)
			$MySelf = new User();
		return $MySelf;
	}
}