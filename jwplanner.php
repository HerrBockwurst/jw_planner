<?php
class JWPlanner {
	public function __construct() {
		require_once 'config.php';
		require_once 'functions.php';
				
		foreach(scandir('classes') AS $File) {
			//Klassen Laden
			if(strpos($File, '.php') === FALSE) continue;
			require_once 'classes/'.$File;
		}
		
		foreach(scandir('pages/frontend') AS $File) {
			//Frontend Laden
			if(strpos($File, '.php') === FALSE) continue;
			require_once 'pages/frontend/'.$File;
		}
		
		foreach(scandir('pages/planner') AS $File) {
			//Planner Laden
			if(strpos($File, '.php') === FALSE) continue;
			require_once 'pages/planner/'.$File;
		}
		
		ContentManager::initContent();	
	}
}