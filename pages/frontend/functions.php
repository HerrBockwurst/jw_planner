<?php
class Frontend_Functions extends StaticPage {
	private function __construct() {
		$this->ClassPath = 'Functions.php';
		$this->PageID = 'Functions';
		$this->Position = POS_FRONTEND;
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Frontend_Functions();
		return $Self;
	}
	
	public function myContent() {
		
	}
}