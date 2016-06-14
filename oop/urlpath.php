<?php
class urlpath {
	public $patharray = array();
	function __construct() {
		$this->patharray = explode('/', substr($_SERVER['PHP_SELF'], 1));		
	}
}

$url = new urlpath();
?>