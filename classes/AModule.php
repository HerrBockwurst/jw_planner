<?php
interface IModule {
	function ContentRequest();
}

abstract class AModule implements IModule {
	public $PageID = NULL, $isDefault = FALSE, $ClassPath = NULL, $MenuItem = NULL, $Permission = NULL, $CSSFile = NULL;
	
	public final function getMyContent() {
		//TODO Hier Permission Pr�fen
		$this->ContentRequest();
	}
}