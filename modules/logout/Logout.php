<?php
class Logout extends Module {
	private function __construct() {
		$this->ClassPath = 'logout';
		$this->MenuItem = new MenuItem("menu Logout", 100, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Logout();
			return $Instance;
	}
	
	private function Handler_Logout() {
		$MySQL = MySQL::getInstance();
		$MySQL->where('sid', session_id());
		if(!$MySQL->delete('sessions')) returnErrorJSON(getString('errors sql'));
		echo json_encode(array('redirect' => PROTO.HOME));
	}
	
	public function ActionDataHandler() {
		
	}
	
	public function ActionLoad() {
		$this->Handler_Logout();
	}
	
	public function ActionSite() {
		
	}
}