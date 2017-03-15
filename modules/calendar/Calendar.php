<?php
class Calendar extends Module {
	private function __construct() {
		$this->Permission = "calendar.entry";
		$this->ClassPath = 'calendar';
		$this->CSSFiles = 'style.css';
		$this->MenuItem = new MenuItem("menu Calendar", 10, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Calendar();
		return $Instance;
	}
	
	public function ActionLoad() {
		switch(getUrl(2)) {
			default:
				printHtml('Overview.html', $this->ClassPath);
				break;				
		}
	}
	
	public function ActionSite() {
	
	}
	
	public function ActionDataHandler() {
		switch(getURL(2)) {
			case 'getHeadline':
				break;
			default:
				break;
		}
	}
}