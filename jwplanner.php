<?php
class JWPlanner {
	private $content;
	
	
	private function __construct() {
		
		require_once 'config.php';
		require_once 'classes/mysql.php';
		require_once 'classes/ContentHandler.php';
		require_once 'classes/User.php';
		require_once 'classes/Language.php';
		require_once 'functions.php';
		
		$this->content = new ContentHandler();
		
		/*
		 * Aktuell werden /load und /site gleich behandelt. Sollten Änderungen nötig sein, müssen die Skripte im ContentHandler angepasst werden.
		 * Aktuell wird nur /load genutzt.
		 */
		User::getInstance()->Auth();
		
		switch(getURL(0)) {
			case 'load':
				$this->content->performLoad();
				break;
			case 'datahandler':
				$this->content->performDatahandler();
				break;
			default:
				//Hier evtl. bei Bedarf performSite draus machen.
				$this->content->performLoad();
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