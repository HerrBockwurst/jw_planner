<?php
interface IModule {
	function ContentRequest();
}

abstract class AModule implements IModule {
	private $MenuItem = NULL, $Permission = NULL;
	public $PageID = NULL, $isDefault = FALSE;
	
	public final function getMyContent() {
		//TODO Hier Permission Prüfen
		$this->ContentRequest();
	}
}