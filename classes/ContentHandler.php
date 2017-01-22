<?php
class ContentHandler {
	
	private $Modules = array(), $StaticPages = array(); 
	
	private function __construct() {
		require_once 'classes/Content.php';
		$modules = scandir('modules');
		$static = scandir('static');
		
		unset($modules[array_search('.', $modules)]);
		unset($modules[array_search('..', $modules)]);
		
		foreach($modules AS $cModule) {
			$files = scandir('modules/'.$cModule);
			foreach($files AS $cFile) 
				if(substr($cFile, -4) == '.php') include_once 'modules/'.$cModule.'/'.$cFile;
		}
		
		foreach($static AS $cStatic) {			
			if(strpos($cStatic, '.php') === false) continue;
			$this->StaticPages[substr($cStatic, 0, strlen($cStatic) - 4)] = "static/".$cStatic;
		}
		
		$this->initModule();
	}
	
	public function getMenuItems($Menu = MENU_ITEM_POS_MAIN) {
		
		$TempMenu = array();
		
		foreach($this->Modules AS $CurrModule) {
			
			$cInstance = $CurrModule::getInstance();
			
			if($cInstance->getMenu() == NULL) continue;
			$MenuItem = $cInstance->getMenu();
			
			$TempMenu[$MenuItem->getOrder()] = $MenuItem;
		}
		
		ksort($TempMenu);
		
		return $TempMenu;
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new ContentHandler();
		return $Instance;
	}	
	
	private function initModule() {
		foreach(get_declared_classes() AS $cClass) {			
			if(get_parent_class($cClass) != 'Module') continue;
			
			$path = $cClass::getInstance()->getModulePath();
			$this->Modules[$path] = $cClass;
		}
	}
	
	private function isStatic($Item) {
		//Gibt im Erfolg den String (Pfad) zur PHP Seite zurück
		$URL = !$Item ? 'index' : $Item;
		return isset($this->StaticPages[$URL]) ? $this->StaticPages[$URL] : false;
	}
	
	private function isModule($Item) {
		//Gibt im Erfolg das Objekt (erzeugt) zurück
		if(!$Item) return false;
		return isset($this->Modules[$Item]) ? $this->Modules[$Item] : false;
	}
	
	public function getModulesByProperty($Property, $Value) {
		
	}
	
	public function getCSS() {
		$static = scandir('static');
		foreach($static AS $cStatic) 
			if(substr($cStatic, -4) == '.css') echo "<link rel=\"stylesheet\" href=\"".PROTO.HOME."/static/".$cStatic."\"></link>";
		foreach($this->Modules AS $cModule)
			$cModule::getInstance()->printCSSLink();
		
	}
	
	public function performDataHandler($Item = NULL) {
		if($Item == NULL) $Item = getURL(1);
		if($this->isStatic($Item)) require_once $this->isStatic($Item);
		elseif($this->isModule($Item)) $this->isModule($Item)::getInstance()->DataHandler();
	}
	
	public function performSite($Item = NULL) {
		if($Item == NULL) $Item = getURL(1);
		if($this->isStatic($Item)) require_once $this->isStatic($Item);
		elseif($this->isModule($Item)) $this->isModule($Item)::getInstance()->Site();
	}
	
	public function performLoad($Item = NULL) {
		if($Item == NULL) $Item = getURL(1);
		if($this->isStatic($Item)) require_once $this->isStatic($Item);
		elseif($this->isModule($Item)) $this->isModule($Item)::getInstance()->Load();
	}
	
}