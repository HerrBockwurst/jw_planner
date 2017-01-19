<?php
class JWPlanner {
	private $content;
	
	function __construct() {
		global $MySQL, $User, $Lang;
		
		require_once 'config.php';
		require_once 'classes/mysql.php';
		require_once 'classes/ContentHandler.php';
		require_once 'classes/User.php';
		require_once 'classes/Language.php';
		require_once 'functions.php';
		
		$MySQL = new MySQL(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
		$this->content = new ContentHandler();
		$User  = new User();
		$Lang = new Language();
		
	}
	
	public function deliverContent() {
		/*
		 * Aktuell werden /load und /site gleich behandelt. Sollten Änderungen nötig sein, müssen die Skripte im ContentHandler angepasst werden.
		 * Aktuell wird nur /load genutzt. 
		 */
		global $User;
		$User->Auth();
		
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
	
	
}