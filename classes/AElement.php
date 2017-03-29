<?php
define('POS_FRONTEND', 1);
define('POS_PLANNER', 2);

interface IElement {
	public static function getInstance(); 
}

abstract class AElement implements IElement {
	protected $ClassPath = NULL;
	protected $Position = NULL;
	protected $MenuItem = NULL;
	protected $IsPublic = FALSE;
	protected $PageID = NULL;
	protected $IsDefaultPage = FALSE;
	
	public function getID() {
		return $this->PageID;
	}
	
	public function isValidContent() {
		if( $this->ClassPath == NULL ||
			$this->Position == NULL ||
			$this->PageID == NULL )
			return FALSE;
		return TRUE;
	}
	
	public function getProperty($Prop) {
		if(!property_exists($this, $Prop)) return FALSE;
		return $this->$Prop;
	}
}