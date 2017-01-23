<?php
class Calendar extends Module {
	private function __construct() {
		$this->Permission = "";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'calendar';
		$this->MenuItem = new MenuItem("menu Calendar", 10, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Calendar();
		return $Instance;
	}
	
	public function ActionLoad() {
		Calendar_Overview::print();
	}
	
	public function ActionSite() {
	
	}
	
	public function ActionDataHandler() {
	
	}
}