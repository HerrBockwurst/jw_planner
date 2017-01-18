<?php
interface IModule {
	public function ActionLoad();
	public function ActionSite();
	public function ActionDataHandler();
	public function PrintCSSLink();
}
abstract class Module implements IModule {
	public final function getModulePath() {
		if(!isset($this->ClassPath)) return false;
		return $this->ClassPath;
	}
	public final function PrintCSSLink() {
		if(!isset($this->CSSFiles)) return;
		$css =& $this->CSSFiles;
		if(is_array($css))
			foreach($css AS $cCSS)
				echo "<link rel=\"stylesheet\" href=\"".PROTO.HOME."/modules/".$this->ClassPath."/".$cCSS."\"></link>";
		else if(is_string($css)) echo "<link rel=\"stylesheet\" href=\"".PROTO.HOME."/modules/".$this->ClassPath."/".$css."\"></link>";
	}
	public final function DataHandler() {
		//Redirect wenn kein Login vorhanden, Ansonsten parsen
		$this->ActionDataHandler();
	}
}
abstract class StaticPage {
	
}
/*
class Module {
	private $permission, $pages = array(), $datahandler = array(), $menuEntry = array(), $css;
	
	/**
 	 * @param SimpleXMLElement $XMLObj
 	 * @param SimpleXMLElement $cPage
 	 *//*
	function __construct($XMLObj, $mod_id) {
		$this->permission = $XMLObj->Permission;
		
		foreach($XMLObj->Pages->children() AS $cPage) 
			$this->pages[$cPage->getName()] = $cPage->__toString();
		
		foreach($XMLObj->Datahandler->children() AS $cHandler) 
			$this->datahandler[$cHandler->getName()] = $cHandler->__toString();
		
		$this->menuEntry['prio'] = intval($XMLObj->MenuString['prio']);
		$this->menuEntry['string'] = $XMLObj->MenuString->__toString();
		
		$this->css = empty($XMLObj->Style) ? null : PROTO.HOME."/modules/$mod_id/".$XMLObj->Style->__toString();
	}
	
	function getMenuEntry() {
		return $this->menuEntry;
	}
	
	function getPermission() {
		return $this->permission;
	}
	
	function getPage(&$id = 'index') {
		return isset($this->pages[$id]) ? $this->pages[$id] : null;
	}
	
	function getHandler(&$id) {
		return isset($this->datahandler[$id]) ? $this->datahandler[$id] : null;
	}
	
	function getCSS() {
		if($this->css == null) return;
		echo "<link rel=\"stylesheet\" href=\"".$this->css."\">";
	}
}*/