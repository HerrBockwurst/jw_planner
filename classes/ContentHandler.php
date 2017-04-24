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
		
		foreach(array_diff(scandir($Dir[self::$Scope]), getDots()) AS $cFile) 
			if(strpos($cFile, '.php') !== FALSE) 
				require_once "{$Dir[self::$Scope]}/{$cFile}";
			elseif(is_dir($cFile))
				foreach(array_diff(scandir("{$Dir[self::$Scope]}/{$cFile}"), getDots()) AS $cSubFile)
					if(strpos($cSubFile, '.php'))
						require_once "{$Dir[self::$Scope]}/{$cFile}/{$cSubFile}";
		
		foreach(get_declared_classes() AS $cClass)
			if(get_parent_class($cClass) == 'AModule')
				self::$Pages[] = new $cClass;
		
	}
	
	private static function getFrontendMenu() {
		$Entrys = array('Start', 'Functions', 'Register', 'Login');
		$String = "";
		foreach($Entrys AS $cEntry)
			$String .= '<li><a href="/'.$cEntry.'">'.getString('Menu '.$cEntry).'</a></li>';
		echo $String;
	}
	
	private static function getAppMenu() {
		
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
			} elseif($Page->isDefault == TRUE && $Hit == NULL)
				$Hit = $Page;
		}
		
		$Page->getMyContent();
		
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