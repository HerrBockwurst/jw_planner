<?php
class HTMLContent {
	private $HTMLString = "";
	public $Valid = FALSE;
	
	public function __construct($File, $ClassPath, $IsFrontend = FALSE) {
		$FilePath = empty($ClassPath) ? $File : $ClassPath.'/'.$File;
		$Path = $IsFrontend ? 'pages/frontend/'.$FilePath : 'pages/planner/'.$FilePath;
		if(!file_exists($Path)) return;
		
		$this->HTMLString = file_get_contents($Path);
		$this->Valid = TRUE;
	}
	
	public function replaceLangTags() {
		$Matches;
		
		preg_match_all('/\(\((.*?)\)\)/', $this->HTMLString, $Matches); //LangTags
		foreach($Matches[0] AS $Match) 
			if(!empty(getString(substr($Match, 2, strlen($Match) - 4))))
				$this->HTMLString = str_replace($Match, getString(substr($Match, 2, strlen($Match) - 4)), $this->HTMLString);
							
		preg_match_all('/\^(.*?)\^/', $this->HTMLString, $Matches); //Konstanten
		foreach($Matches[0] AS $Match)
			if(defined(substr($Match, 1, strlen($Match) - 2)))
				$this->HTMLString = str_replace($Match, constant(substr($Match, 1, strlen($Match) - 2)), $this->HTMLString);

		$this->HTMLString = str_replace('\n', '<br />', $this->HTMLString);
			
	}
	
	public function replace($Data) {
		$Needle = array();
		$Replacements = array();
		foreach($Data AS $PlaceHolder => $Replacement) {
			$Needle[] = "(".$PlaceHolder.")";
			$Replacements[] = $Replacement;
		}
		$this->HTMLString = str_replace($Needle, $Replacements, $this->HTMLString);
	}
	
	public function display() {
		echo $this->HTMLString;
	}
}