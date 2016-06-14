<?php
class urlpath {
	private $patharray = array();
	function __construct() {
		$this->patharray = explode('/', substr($_SERVER['REQUEST_URI'], 1));		
	}
	
	function value($index) {
		return $this->patharray[$index];
	}
}

$url = new urlpath();
?>