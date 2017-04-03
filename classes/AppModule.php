<?php
abstract class AppModule extends AElement {	
	protected $Permission = NULL;	
	
	public final function getMyContent() {
		if($this->Permission != NULL && !User::getMyself()->hasPermission()) return; //Teste Permission für Seite
		$this->myContent();
	}
}