<?php
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0 , 2);
switch ($lang) {
	case 'de':
		break;
	default:
		break;
}


class language {
	function __construct() {
		return simplexml_load_file('language/de_de.xml');	
	}
}
?>