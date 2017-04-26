<?php
namespace App;

class UserManagement extends \AModule {
	function __construct() {
		$this->PageID = 'usermanagement';
		$this->ClassPath = 'app/usermanagement';
		$this->MenuItem = new \MenuItem($this->PageID, getString('Menu UserManagement'), MENU_USER, 10);
		$this->CSSFile = 'style.css';
	}
	
	private function getUserCards() {
		
	}
	
	function ContentRequest() {
		switch(getURL(1)) {
			default:
				$html = new \HTMLTemplate('UserCards.html', $this->ClassPath);
				$html->replaceLangTag();
				$html->display();
		}
		
	}
}