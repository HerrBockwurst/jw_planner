<?php
define('MENU_DASHBOARD', 1);
define('MENU_CALENDAR', 2);
define('MENU_USER', 3);
define('MENU_SYSTEM', 4);

class MenuItem {
	public $URL, $String, $Parent, $Position;
	
	function __construct($URL, $String, $Parent, $Position) {
		$this->URL = $URL;
		$this->String = $String;
		$this->Parent = $Parent;
		$this->Position = $Position;
	}
}