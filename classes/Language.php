<?php
class Language {
	private $lang;
	
	private function __construct() {
		//TODO Sprache unterscheiden
		$this->lang = simplexml_load_file("language/de_de.xml");
	}
	
	public static function getInstance() {
		static $Instance = NULL;
		if($Instance === NULL)
			$Instance = new Language();
		return $Instance;
	}
	
	public function getValue($tree) {
		$exTree = explode(" ", $tree);
		$retval = false;
		$tmpObj = $this->lang;		
		
		foreach($exTree AS $currObj) {
			$tmpObj = $tmpObj->$currObj;
		}
		return strval($tmpObj);
	}
}