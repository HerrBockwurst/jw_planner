<?php
class Messages extends Module {
	private function __construct() {
		$this->Permission = 'messages.use';
		$this->CSSFiles = "style.css";
		$this->ClassPath = 'messages';
		$this->MenuItem = new MenuItem("menu messages", 20, $this->ClassPath, $this->Permission);
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Messages();
			return $Instance;
	}

	public function ActionDataHandler() {
	}
	
	public function ActionLoad() {
		printHtml('Overview.html', $this->ClassPath);
	}
	
	public function ActionSite() {
	
	}
}