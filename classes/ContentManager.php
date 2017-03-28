<?php
class ContentManager {
	private static $Content = array();
	private static $CurrentClass = NULL;
	
	private function __construct() {		
	}
	
	public static function initContent() {
		foreach(get_declared_classes() AS $cClass) {
			if(get_parent_class($cClass) != 'AppModule' && get_parent_class($cClass) != 'StaticPage') continue;
			
			self::$Content[] = $cClass::getInstance();
		}
		
		self::getContent();
	}	
	
	public static function getContent() {
		print_r(self::$Content);
	}
	
	public static function getCurrentClass() {
		return self::$CurrentClass;
	}
}