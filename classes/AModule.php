<?php
interface IModule {
	function ContentRequest();
}

abstract class AModule implements IModule {
	private $PageID = NULL,
		$MenuItem = NULL,
		$Permission = NULL;
	
	public final function getMyContent() {
		//TODO Hier Permission Prüfen
		$this->ContentRequest();
	}
}