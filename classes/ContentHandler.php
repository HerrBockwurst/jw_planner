<?php
class ContentHandler {
	
	private $Modules = array(), $StaticPages = array(); 
	
	function __construct() {
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
	
	
	private function initModule() {
		foreach(get_declared_classes() AS $cClass) {			
			if(get_parent_class($cClass) != 'Module') continue;
			
			$path = (new $cClass())->getModulePath();
			$this->Modules[$path] = $cClass;
		}
	}
	
	private function isStatic() {
		$URL = !getURL(1) ? 'index' : getURL(1);
		return isset($this->StaticPages[$URL]) ? $this->StaticPages[$URL] : false;
	}
	
	private function isModule() {
		if(!getURL(1)) return false;
		return isset($this->Modules[getURL(1)]) ? $this->Modules[getURL(1)] : false;
	}
	
	public function getCSS() {
		$static = scandir('static');
		foreach($static AS $cStatic) 
			if(substr($cStatic, -4) == '.css') echo "<link rel=\"stylesheet\" href=\"".PROTO.HOME."/static/".$cStatic."\"></link>";
		foreach($this->Modules AS $cModule)
			(new $cModule)->printCSSLink();
		
	}
	
	public function performDataHandler() {
		if($this->isStatic()) require_once $this->isStatic();
		else if($this->isModule()) (new $this->isModule())->DataHandler();
	}
	
	public function performSite() {
		if($this->isStatic()) require_once $this->isStatic();
		else if($this->isModule()) (new $this->isModule())->ActionSite();
	}
	
	public function performLoad() {
		if($this->isStatic()) require_once $this->isStatic();
		else if($this->isModule()) (new $this->isModule())->ActionLoad();
	}
	
}