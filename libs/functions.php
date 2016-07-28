<?php
if(!defined('index')) exit;

function getURL($int) {
	$url = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	if(!key_exists($int, $url)) return false;
	return $url[$int]; 
}

function checkURL($int, $search) {
	if(getURL($int) == $search) return true;
	return false;
}

function displayString($tree) {
	global $lang;
	echo $lang->get($tree);
	return;
}

function getString($tree) {
	global $lang;
	return $lang->get($tree);
}

function registerModul($data) {
	global $ModulHandler;
	$ModulHandler->registerModul($data);
}

function checkModul($name) {
	global $ModulHandler;
	return $ModulHandler->check($name);
}

function loadModules() {
	/*
	 * modules Ordner durchsuchen und index in jeden unterordner starten
	 */
	if($handle = opendir('modules')):
		while(false !== ($entry = readdir($handle))):
			if($entry != '.' && $entry != '..' && is_dir('modules/'.$entry)):
				if(file_exists('modules/'.$entry.'/register.php'))
					require_once 'modules/'.$entry.'/register.php';
			endif;
		endwhile;
		closedir($handle);
	endif;
}
function destruct($var) {
	$var->__destruct();
}

function addDataHandler($path) {
	global $DataHandler;
	if(!isset($DataHandler)) $DataHandler = array();
	
	if($path[0] == 1) $path[0] = 'modules/'; 
	elseif($path[0] == 2) $path[0] = 'pages/';
	
	$DataHandler[$path[1]] = $path[0].$path[1]."/".$path[2];
}

function getDataHandler($id) {
	global $DataHandler;
	if(!key_exists($id, $DataHandler)) return false;
	
	require_once $DataHandler[$id];
}

function displayHandlerURL($name) {
	echo PROTO.HOME.'/ajax/datahandler/'.$name;
}