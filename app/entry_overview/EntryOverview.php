<?php
namespace App;

class EntryOverview extends \AModule {
	function __construct() {
		$this->PageID = 'entryoverview';
		$this->ClassPath = 'app/entry_overview';
		$this->MenuItem = new \MenuItem($this->PageID, getString('Menu EntryOverview'), MENU_DASHBOARD, 10);
		$this->Scope = SCOPE_DESKTOP_APP;
	}	
	
	function ContentRequest() {
		switch(getURL(1)) {
			default:
				echo "Entryoverview";
		}
		
	}
}