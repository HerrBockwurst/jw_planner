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
	if(!testAjax())
		echo $string;
	else 
		echo json_encode(array('error' => $string));
	exit;
	
}

function removeFilterInverse($Array, $Filter) {
	foreach($Array AS $key => $cEntry) 
		if(!in_array($cEntry, $Filter)) unset($Array[$key]);
	
	return $Array;
}

function parseUsername($name, $Reserved) {
	$username = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
	$username = str_replace('ä', 'ae', $username);
	$username = str_replace('ö', 'oe', $username);
	$username = str_replace('ü', 'ue', $username);
	$username = str_replace('ß', 'ss', $username);
	$username = preg_replace('/[^A-Za-z\-]/', '', $username);
	$username = strtolower($username);
	
	$Counter = 2;
	$MySQL = MySQL::getInstance();
	$NewUsername = $username;
	
	$MySQL->where('uid', $NewUsername);
	while($MySQL->count('users', 'uid') !== 0 || in_array($NewUsername, $Reserved)) {
		$NewUsername = $username.'-'.$Counter;
		$Counter++;
		$MySQL->where('uid', $NewUsername);
	}
	
	return $NewUsername;
}