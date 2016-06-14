<?php
class urlpath {
	public $patharray = array();
	function __construct() {
		$this->patharray = explode('/', substr($_SERVER['PATH_INFO'], 1));		
	}
}

$url = new urlpath();
?>