<?php
define('SCOPE_API', 0);
define('SCOPE_FRONTEND', 1);
define('SCOPE_DESKTOP_APP', 2);

class ContentHandler {
	private static $Scope;
	
	public static function loadPages() {
		$Dir = array(
				SCOPE_API => 'api',
				SCOPE_DESKTOP_APP => 'app',
				SCOPE_FRONTEND => 'frontend'
		);
		
		foreach(array_diff(scandir($Dir[self::$Scope]), getDots()) AS $cFile) 
			if(strpos($cFile, '.php'))
				require_once $cFile;
			elseif(is_dir($cFile))
				foreach(array_diff(scandir("{$Dir[self::$Scope]}/{$cFile}"), getDots()) AS $cFile)
					if(strpos($cFile, '.php'))
						require_once $cFile;
	}
	
	public static function setScope($Scope) {
		self::$Scope = $Scope;
	}
}