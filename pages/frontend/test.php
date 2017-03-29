<?php
class TestPage extends StaticPage {
	private function __construct() {
		$this->ClassPath = 'test';
		$this->PageID = 'FrontendIndex';
		$this->Position = POS_FRONTEND;
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new TestPage();
		return $Self;
	}
}