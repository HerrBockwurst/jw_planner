<?php
define('CONTENT_TYPE_APP', 1);
define('CONTENT_TYPE_FRONTEND', 2);
define('CONTENT_TYPE_API', 3);

class ContentManager {
	private static $Content = array(), $CurrentClass = NULL, $CSSFiles = array();
	public static $ContentType; //Switch ob gerade Frontend oder App oder API aufgerufen wird, wird durch die getXXXContent gesetzt.
	
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
		switch(strtolower(getURL(0))) {
			case 'app':				
				self::getAppContent();
				break;
			default:				
				self::getFrontendContent();
				break;
		}		
	}
	
	public static function getMenuBar() {
		
	}
	
	public static function addCSSFile($File) {
		self::$CSSFiles[] = $File;
	}
	
	public static function getCSSFiles() {
		foreach(self::$CSSFiles AS $File)
			echo '<link rel="stylesheet" href="'.PROTO.HOME.'/'.$File.'"></link>';

		foreach(self::getFilteredPages(array('CSSFile' => '!NULL')) AS $Content) {
			if($Content::getInstance()->Position == POS_FRONTEND) $Subdir = 'frontend';
			elseif($Content::getInstance()->Position == POS_PLANNER) $Subdir = 'planner';
		}
			echo '<link rel="stylesheet" href="'.PROTO.HOME.'/pages/'.$Subdir.'/'.$Content->ClassPath.'/'.$Content->CSSFile.'"></link>';
	}
	
	private static function getAppContent() {
		self::$ContentType = CONTENT_TYPE_APP;
		if(!isset($_POST['isAjax'])) include_once 'pages/header.php';		
		if(!User::getMyself()->IsLoggedIn) 
			$AppContent = self::getFilteredPages(array('PageID' => 'login'));
		else {
			$ContentID = empty(getURL(1)) ? 'default' : getURL(1);			
			$AppContent = $ContentID == 'default' ?
				self::getFilteredPages(array('Position' => POS_PLANNER, 'IsDefaultPage' => TRUE)) :
				self::getFilteredPages(array('Position' => POS_PLANNER, 'PageID' => $ContentID)) ;		
			
			if(empty($AppContent)) $AppContent = self::getFilteredPages(array('Position' => POS_PLANNER, 'IsDefaultPage' => TRUE));
		}
		
		$AppContent = $AppContent[0];
		
		$AppContent->getMyContent();
		
		if(!isset($_POST['isAjax'])) include_once 'pages/footer.php';
	}
	
	private static function getFrontendContent() {
		self::$ContentType = CONTENT_TYPE_FRONTEND;
		if(!isset($_POST['isAjax'])) include_once 'pages/header.php';
		
		$ContentID = empty(getURL(0)) ? 'default' : getURL(0);
		
		$FrontendContent = $ContentID == 'default' ? 
			self::getFilteredPages(array('Position' => POS_FRONTEND, 'IsDefaultPage' => TRUE)) : 
			self::getFilteredPages(array('Position' => POS_FRONTEND, 'PageID' => $ContentID)) ;
		
		if(empty($FrontendContent)) $FrontendContent = self::getFilteredPages(array('Position' => POS_FRONTEND, 'IsDefaultPage' => TRUE));  
		$FrontendContent = $FrontendContent[0];
		
		$FrontendContent->getMyContent();
		
		if(!isset($_POST['isAjax'])) include_once 'pages/footer.php';
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