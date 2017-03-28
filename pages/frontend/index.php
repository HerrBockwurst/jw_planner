<?php
class Frontend_Index extends StaticPage {
	private function __construct() {
		$this->ClassPath = 'index';
	}
	
	public static function getInstance() {
		static $Self = NULL;
		if($Self === NULL)
			$Self = new Frontend_Index();
		return $Self;
	}
}