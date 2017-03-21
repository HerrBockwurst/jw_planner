<?php
class JWPlanner {	
	
	private function __construct() {
		
		require_once 'config.php';
		foreach(scandir('classes') AS $cFile) {
			if($cFile == '.' || $cFile == '..') continue;
			require_once 'classes/'.$cFile;
		}		
		require_once 'functions.php';
		
		/*
		 * Aktuell werden /load und /site gleich behandelt. Sollten Änderungen nötig sein, müssen die Skripte im ContentHandler angepasst werden.
		 * Aktuell wird nur /load genutzt.
		 */
		
		CalendarManager::cleanUpPosts();
		User::getInstance()->Auth();
		$ContentHandler = ContentHandler::getInstance();
		
		switch(getURL(0)) {
			case 'load':
				$ContentHandler->performLoad();
				break;
			case 'datahandler':
				$ContentHandler->performDatahandler();
				break;
			case 'api':
				require_once 'api/apis.php';
				new APIHandler();
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