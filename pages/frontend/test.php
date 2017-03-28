<?php
class TestPage extends StaticPage {
	private function __construct() {
		$this->ClassPath = 'test';
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new TestPage();
			return $Self;
	}
}