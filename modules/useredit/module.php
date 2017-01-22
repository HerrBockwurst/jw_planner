<?php
class UserEdit extends Module {
	 // = 'useredit';
	
	private function __construct() {
		$this->Permission = "";
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'useredit';
		$this->MenuItem = new MenuItem("menu UserEdit", 50, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new UserEdit();
		return $Instance;
	}
	
	public function ActionLoad() {
		
	}
	public function ActionSite() {
		
	}
	public function ActionDataHandler() {
		
	}
}