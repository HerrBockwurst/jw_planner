<?php
define('SCOPE_API', 0);
define('SCOPE_FRONTEND', 1);
define('SCOPE_DESKTOP_APP', 2);

class ContentHandler {
	private static $Scope, $Pages = array();
	
	public static function loadPages() {
		$Dir = array(
				SCOPE_API => 'api',
				SCOPE_DESKTOP_APP => 'app',
				SCOPE_FRONTEND => 'frontend'
		);
		
		foreach($Dir AS $cDir)
			foreach(array_diff(scandir($cDir), getDots()) AS $cFile)			
				if(strpos($cFile, '.php') !== FALSE) 
					require_once "{$cDir}/{$cFile}";
					elseif(is_dir("{$cDir}/$cFile"))
					foreach(array_diff(scandir("{$cDir}/{$cFile}"), getDots()) AS $cSubFile)
						if(strpos($cSubFile, '.php'))
							require_once "{$cDir}/{$cFile}/{$cSubFile}";
		
		foreach(get_declared_classes() AS $cClass)
			if(get_parent_class($cClass) == 'AModule')
				self::$Pages[] = new $cClass;
		
	}
	
	public static function printCSS() {
		$String = "";
		foreach(self::$Pages AS $cPage) 
			/**
			 * @var $cPage AModule
			 */
			if(User::getMyself()->hasPermission($cPage->Permission) && $cPage->CSSFile != NULL)
				$String .= "<link rel=\"stylesheet\" href=\"".PROTO.HOME."/{$cPage->ClassPath}/{$cPage->CSSFile}\"></link>";
			
		echo $String;
	}
	
	private static function getFrontendMenu() {
		$Entrys = array('Start', 'Functions', 'Register', 'Login');
		$String = "";
		foreach($Entrys AS $cEntry)
			$String .= '<li><a href="/'.$cEntry.'">'.getString('Menu '.$cEntry).'</a></li>';
		echo $String;
	}
	
	private static function getAppMenu() {
		$String = "";
		$Cats = array(
				MENU_DASHBOARD => getString('Menu Cat_Dashboard'),
				MENU_CALENDAR => getString('Menu Cat_Calendar'),
				MENU_USER => getString('Menu Cat_User'),
				MENU_SYSTEM => getString('Menu Cat_System')
		);
		$PrintCats = array();
		foreach($Cats AS $CatKey => $cCat)
			foreach(self::$Pages AS $cPage)
				if($cPage->MenuItem != NULL && User::getMyself()->hasPermission($cPage->Permission) && $cPage->MenuItem->Parent == $CatKey)
					$PrintCats[$CatKey][$cPage->MenuItem->Position] = array('url' => $cPage->MenuItem->URL, 'string' => $cPage->MenuItem->String);

		foreach($PrintCats AS $CatKey => $cCat) {
			ksort($cCat);
			$String .= "<li><a>{$Cats[$CatKey]}</a><ul>";			
			foreach($cCat AS $MenuItem)
				$String .= "<li><a href=\"{$MenuItem['url']}\">{$MenuItem['string']}</a></li>";
			$String .= '</ul></li>';
		}
		
		echo $String;
				
	}
	
	public static function getMenu() {
		switch(self::$Scope) {
			case SCOPE_DESKTOP_APP:
				self::getAppMenu();
				break;
			default:
				self::getFrontendMenu();
				break;
		}
	}
	
	private static function loadPage() {
		$ReqPage = getURL(0);
		$Hit = NULL;
		
		foreach(self::$Pages AS $Page) { //Gewünschte Seite anzeigen
			if($Page->PageID == $ReqPage) {
				$Hit = $Page;
				break;
			} elseif($Page->isDefault == TRUE && $Hit == NULL && $Page->Scope == self::$Scope)				
				$Hit = $Page;
		}
		
		$Hit->getMyContent();		
	}
	
	public static function deliverContent() {
		if(!testAjax()) {
			require_once 'serialPages/header.php';
			require_once 'serialPages/menubar.php';
		}		
		self::loadPage();
		if(!testAjax()) require_once 'serialPages/footer.php';
	}
	
	public static function setScope($Scope) {
		self::$Scope = $Scope;
	}
}