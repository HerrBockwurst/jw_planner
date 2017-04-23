<?php
function getDots() {
	return array('.', '..');
}

function testAjax(): bool {
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') return FALSE;
	return TRUE;
}

function getURL($int) {
	$url = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	if(!key_exists($int, $url)) return false;
	return strtolower($url[$int]);
}

function getString($tree) {
	return Language::getInstance()->getValue($tree);
}

function displayString($tree) {
	echo getString($tree);
}

function returnErrorJSON($string) {
	echo json_encode(array('error' => $string));
	exit;
}

function removeFilterInverse($Array, $Filter) {
	foreach($Array AS $key => $cEntry) 
		if(!in_array($cEntry, $Filter)) unset($Array[$key]);
	
	return $Array;
}