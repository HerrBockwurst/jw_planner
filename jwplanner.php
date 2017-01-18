<?php
class JWPlanner {
	private $content;
	
	function __construct() {
		require_once 'config.php';
		require_once 'classes/mysql.php';
		require_once 'classes/ContentHandler.php';
		require_once 'classes/User.php';
		require_once 'classes/Language.php';
		require_once 'functions.php';
		
		$GLOBALS['mysql'] = new MySQL(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
		$this->content = new ContentHandler();
		$GLOBALS['user']  = new User();
		$GLOBALS['lang'] = new Language();
		
	}
	
	public function deliverContent() {
		
		global $user;
		$user->Auth();
		
		switch(getURL(0)) {
			case 'load':
				$this->content->performLoad();
				break;
			case 'datahandler':
				$this->content->performDatahandler();
				break;
			default: 
				$this->content->performSite();
				break;				
		}
	}
	
	
}