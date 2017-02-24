<?php
class JWPlanner {	
	
	private function __construct() {
		
		require_once 'config.php';
		require_once 'classes/mysql.php';
		require_once 'classes/ContentHandler.php';
		require_once 'classes/User.php';
		require_once 'classes/Language.php';
		require_once 'classes/MenuItem.php';
		require_once 'classes/PermissionManager.php';
		require_once 'classes/RoleManager.php';
		require_once 'classes/Foreigner.php';
		require_once 'classes/VersManager.php';
		require_once 'classes/GroupManager.php';
		require_once 'functions.php';
		
		/*
		 * Aktuell werden /load und /site gleich behandelt. Sollten Änderungen nötig sein, müssen die Skripte im ContentHandler angepasst werden.
		 * Aktuell wird nur /load genutzt.
		 */
		User::getInstance()->Auth();
		$ContentHandler = ContentHandler::getInstance();
		
		switch(getURL(0)) {
			case 'load':
				$ContentHandler->performLoad();
				break;
			case 'datahandler':
				$ContentHandler->performDatahandler();
				break;
			default:
				//Hier evtl. bei Bedarf performSite draus machen.
				$ContentHandler->performLoad();
				break;
		}
		
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL) 
			$Instance = new JWPlanner();
		return $Instance;
	}
}