<?php
class language {
	public $lang;
	function __construct() {
		$this->lang = simplexml_load_file('language/de_de.xml');
		
	}
	
	function getString($id) {
		echo $this->lang->menu->calendar;
	}
}
?>