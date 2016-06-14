<?php
/*$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0 , 2);
switch ($lang) {
	case 'de':
		break;
	default:
		break;
}
*/

class language {
	private $lang;
	function __construct() {
		$this->lang = simplexml_load_file('language/de_de.xml');	
	}
	
	function display($tree) {
		echo $this->lang->$tree;
	}
}
?>