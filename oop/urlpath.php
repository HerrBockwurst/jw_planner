<?php
checkIndex();

class urlpath {
	private $patharray = array();
	function __construct() {
		$this->patharray = explode('/', substr($_SERVER['REQUEST_URI'], 1));		
	}
	
	function value($index) {
		if(isset($this->patharray[$index]))
			return $this->patharray[$index];
		else 
			return '';
	}
}

$url = new urlpath();
?>