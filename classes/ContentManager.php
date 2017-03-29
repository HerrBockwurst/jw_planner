<?php
class ContentManager {
	private static $Content = array();
	private static $CurrentClass = NULL;
	
	private function __construct() {		
	}
	
	public static function initContent() {
		foreach(get_declared_classes() AS $cClass) {
			if(get_parent_class($cClass) != 'AppModule' && get_parent_class($cClass) != 'StaticPage') continue;
			
			$Content = $cClass::getInstance();
			if(!$Content->isValidContent()) continue;
			
			self::$Content[] = $Content;
		}		
	}	
	
	public static function getContent() {
		print_r(self::$Content);
	}
	
	public static function getCurrentClass() {
		return self::$CurrentClass;
	}
	
	public static function getPage($ID) {
		foreach(self::$Content AS $cPage) {
			if($cPage->getID() == $ID) 
				return $cPage;			
		}
		return NULL;
	}
	
	public static function getFilteredPages($Filter) {
		$RetVal = array();		
		
		foreach(self::$Content AS $cPage) {
			$SkipPage = FALSE;
			foreach($Filter AS $Prop => $Val) {
				if($cPage->getProperty($Prop) === FALSE) {
					$SkipPage = TRUE;
					break;
				}
				if($cPage->getProperty($Prop) != $Val) {
					$SkipPage = TRUE;
					break;
				}
			}
			
			if($SkipPage) continue;
			$RetVal[] = $cPage;
		}
		
		return $RetVal;
	}
}