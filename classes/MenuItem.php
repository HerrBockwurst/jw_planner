<?php

class MenuItem {
	public $String, $URL, $Parent, $Priority;
	
	
	function __construct($String, $URL, $Parent, $Prio) {
		$this->String = $String;
		$this->URL = $URL;
		$this->Parent = $Parent;
		$this->Priority = $Prio;
	}
	
}