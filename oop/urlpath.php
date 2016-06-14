<?php
class urlpath {
	public $patharray = array();
	function __construct() {
		$this->patharray = explode('/', $_SERVER['PHP_SELF']);		
	}
}

$url = new urlpath();
?>