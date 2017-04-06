<?php
abstract class AppModule extends AElement {	
	public $Permission = NULL;	
	
	public final function getMyContent() {
		if($this->Permission != NULL && !User::getMyself()->hasPermission($this->Permission)) return; //Teste Permission für Seite
		$this->myContent();
	}
	
	public final function getMenuItemPrio() {
		if($this->MenuItem == NULL) return FALSE;
		return $this->MenuItem->Priority;
	}
	
	public final function getMenuItem($TopCat) {
		if($this->MenuItem == NULL) return FALSE;
		if($this->MenuItem->Parent != $TopCat) return FALSE;
		return array($this->MenuItem->URL => $this->MenuItem->String);
	}
}