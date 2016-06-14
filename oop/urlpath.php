<?php
class urlpath {
	public $patharray = array();
	function __construct() {
		$this->patharray = explode('/', substr($_SERVER['REQUEST_URI'], 1));		
	}
}

$url = new urlpath();
?>