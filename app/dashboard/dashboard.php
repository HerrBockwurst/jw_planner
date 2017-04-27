<?php
namespace App;

class Login extends \AModule {
	function __construct() {
		$this->PageID = 'dashboard';
		$this->ClassPath = 'app/dashboard';
		$this->isDefault = TRUE;
		$this->MenuItem = new \MenuItem($this->PageID, getString('Menu Dashboard'), MENU_DASHBOARD, 0);
		$this->Scope = SCOPE_DESKTOP_APP;
	}	
	
	function ContentRequest() {
		switch(getURL(1)) {
			default:
				echo "Dashboardi";
		}
		
	}
}