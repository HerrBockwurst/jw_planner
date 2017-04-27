<?php
class HTMLTemplate {
	private $HTML;
	
	function __construct($File, $Path) {				
		if(file_exists("{$Path}/{$File}"))
			$this->HTML = file_get_contents("{$Path}/{$File}");
	}
	
	function replace($Replacer) {
		foreach($Replacer AS $Find => $Replace)
			$this->HTML = preg_replace("/\({$Find}\)/", $Replace, $this->HTML);
	}
	
	function replaceLangTag() {
		$String = $this->HTML;
		$Matches;
		preg_match_all('/\[\((.*?)\)\]/', $String, $Matches);
		foreach($Matches[0] AS $Match)
			$String = str_replace($Match, getString(substr($Match, 2, strlen($Match) - 4)), $String);
			
		preg_match_all('/\^(.*?)\^/', $String, $Matches);
		foreach($Matches[0] AS $Match)
			if(defined(substr($Match, 1, strlen($Match) - 2)))
				$String = str_replace($Match, constant(substr($Match, 1, strlen($Match) - 2)), $String);
					
		$String = str_replace('\n', '<br />', $String);
		
		$this->HTML = $String;
	}
	
	public function display() {
		echo $this->HTML;
	}
}