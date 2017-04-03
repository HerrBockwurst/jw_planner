<?php
class JWPlanner {
	public function __construct() {
		require_once 'config.php';
		require_once 'functions.php';
		
		$Dots = array('.', '..');
				
		foreach(scandir('classes') AS $File) {
			//Klassen Laden
			if(strpos($File, '.php') === FALSE) continue;
			require_once 'classes/'.$File;
		}
		
		foreach(scandir('pages/frontend') AS $File) {
			//Frontend Laden
			if(strpos($File, '.php') !== FALSE) 
				require_once 'pages/frontend/'.$File;
			elseif(strpos($File, '.css') !== FALSE)
				ContentManager::addCSSFile('pages/frontend/'.$File);
		}
		
		foreach(array_diff(scandir('pages/planner'), $Dots) AS $File) {
			//Planner Laden	
			if(is_dir('pages/planner/'.$File)) 
				foreach(array_diff(scandir('pages/planner/'.$File), $Dots) AS $ChildFile) 
					if(strpos($ChildFile, '.php') !== FALSE)
						require_once 'pages/planner/'.$File.'/'.$ChildFile;					
			elseif(strpos($File, '.php') !== FALSE)
				require_once 'pages/planner/'.$File;
		}
		
		foreach(scandir('pages') AS $File) {
			//CSS in Pages Laden
			if(strpos($File, '.css') !== FALSE)
				ContentManager::addCSSFile('pages/'.$File);
		}
		
		ContentManager::initContent();
		ContentManager::getContent();
	}
}