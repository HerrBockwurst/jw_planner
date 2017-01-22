<?php
class Dashboard extends Module {
	private function __construct() {
		$this->Permission = "";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'dashboard';
		$this->MenuItem = new MenuItem("menu Dashboard", 0, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Dashboard();
			return $Instance;
	}
	
	public function ActionLoad() {
		Dashboard_welcome::print();
	}
	public function ActionSite() {
	
	}
	public function ActionDataHandler() {
	
	}
}