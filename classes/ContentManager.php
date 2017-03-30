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
	
	public static function getCommonPage($PageName) {
		if(strpos($PageName, '.php') === FALSE) $PageName .= '.php';
		
		if(!file_exists('pages/'.$PageName)) return;
		require_once 'pages/'.$PageName;
	}
	
	public static function getContent() {
		if(!isset($_POST['isAjax'])) include_once 'pages/header.php';
				
		switch(getURL(0)) {
			case 'app':				
				self::getAppContent();
				break;
			default:
				self::getFrontendContent();
				break;
		}
		if(!isset($_POST['isAjax'])) include_once 'pages/footer.php';
	}
	
	private static function getAppContent() {
		
	}
	
	private static function getFrontendContent() {
		$ContentID = empty(getURL(0)) ? 'default' : getURL(0);
		
		$FrontendContent = $ContentID == 'default' ? 
			self::getFilteredPages(array('Position' => POS_FRONTEND, 'IsDefaultPage' => TRUE)) : 
			self::getFilteredPages(array('Position' => POS_FRONTEND, 'PageID' => $ContentID)) ;
		
		if(empty($FrontendContent)) $FrontendContent = self::getFilteredPages(array('Position' => POS_FRONTEND, 'IsDefaultPage' => TRUE));  
		$FrontendContent = $FrontendContent[0];
		
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
				
				$Negate = strpos($Val, '!') === 0 ? TRUE : FALSE;				
				$Val = strpos($Val, '!') === 0 ? substr($Val, 1) : $Val;				
				
				if(strcasecmp($Val, 'NULL') == 0) $Val = NULL; //String "NULL" zu NULL -> Wird benötigt um Werte auf Negiertes Null vergleichen zu können
				
				if($cPage->getProperty($Prop) === FALSE) {
					$SkipPage = TRUE;
					break;
				}
				if($cPage->getProperty($Prop) != $Val && $Negate == FALSE) {
					$SkipPage = TRUE;
					break;
				}
				if($cPage->getProperty($Prop) == $Val && $Negate == TRUE) {
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