<?php
interface IModule {
	public function ActionLoad();
	public function ActionSite();
	public function ActionDataHandler();
	public function PrintCSSLink();
	public static function getInstance();
}
abstract class Module implements IModule {
	public $isPublic = FALSE;
	protected $MenuItem = NULL;
	protected $Permission = "";
	protected $ClassPath, $CSSFiles;
	
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
		if(!User::getInstance()->IsLoggedIn && !$this->isPublic) {
			echo json_encode(array("redirect" => PROTO.HOME));
			exit;
		}
		
		//Teste Permission
		if($this->Permission != "" && !User::getInstance()->hasPerm($this->Permission)) {
			returnErrorJSON(getString('errors noPerm'));
			exit;
		}	
		
		$this->ActionDataHandler();
	}
	
	public final function getMenu($Menu = MENU_ITEM_POS_MAIN) {
		if(!$this->MenuItem instanceof MenuItem) return NULL;
		if($this->MenuItem->getMenuPos() != $Menu) return NULL;
		return $this->MenuItem;
	}
	
	public final function Load() {
		//Redirect wenn kein Login
		if(!User::getInstance()->IsLoggedIn && !$this->isPublic) {
			echo json_encode(array("redirect" => PROTO.HOME));
			exit;
		}
		
		//Teste Permission
		if($this->Permission != "" && !User::getInstance()->hasPerm($this->Permission)) {
			returnErrorJSON(getString('errors noPerm'));
			exit;
		}
		
		$this->ActionLoad();
	}
}