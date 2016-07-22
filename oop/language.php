<?php
class language {
	private $lang;
	
	function __construct() {
		
		$locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0 , 2);
		
		switch ($locale) {
			case 'de':
				$this->lang = simplexml_load_file('language/de.xml');
				break;
			default:
				$this->lang = simplexml_load_file('language/de.xml');
				break;
		}
	}
	
	public function get($tree) {
		$expl = explode('>', $tree);
		$tmpobj = $this->lang;
		foreach ($expl as $part) {
			$tmpobj = $tmpobj->$part;
		}
			
		return $tmpobj;
	}
}

$lang = new language();