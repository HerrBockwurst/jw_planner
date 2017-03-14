<?php

function loadHtml($FileName, $ModulFolder) {
	return file_exists('modules/'.$ModulFolder.'/'.$FileName) ? file_get_contents('modules/'.$ModulFolder.'/'.$FileName) : "";
}

function removeWhiteSpace($String, $Space = '/\\t|\\n|\\r/') {
	return preg_replace("/\\t|\\n|\\r/", "", $String);
}

function validateEmail($mail) {
	$Pattern = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
	return empty(preg_grep($Pattern, array($mail))) ? FALSE : TRUE;	
}

function parseUsername($name) {
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
	while($MySQL->count('users', 'uid') !== 0) {
		$NewUsername = $username.'-'.$Counter;
		$Counter++;
		$MySQL->where('uid', $NewUsername);
	}
	
	return $NewUsername;
}

function replaceLangTags($String) {
	$Matches;
	preg_match_all('/==(.*?)==/', $String, $Matches);	
	foreach($Matches[0] AS $Match) 
		$String = str_replace($Match, getString(substr($Match, 2, strlen($Match) - 4)), $String);
	
	preg_match_all('/\^(.*?)\^/', $String, $Matches);
	foreach($Matches[0] AS $Match)
		if(defined(substr($Match, 1, strlen($Match) - 2)))
			$String = str_replace($Match, constant(substr($Match, 1, strlen($Match) - 2)), $String);
	
	$String = str_replace('\n', '<br />', $String);
			
	return $String;
}

function printHtml($FileName, $ModulFolder) {
	echo replaceLangTags(loadHtml($FileName, $ModulFolder));
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

function isLoggedIn() {
	return User::getInstance()->IsLoggedIn;
}

function getSaltedPassword($Pass) {
	return $Pass.SALT;
}

function needAuth() {
	if(!isLoggedIn()) exit;
}

function stringToColorCode($str) {
	$code = dechex(crc32($str));
	$code = substr($code, 0, 6);
	$code = hexdec(substr($code ,0,2)).",".hexdec(substr($code ,2,2)).",".hexdec(substr($code ,4,2)).",0.5";

	return $code;
}
function repUmlaute($str): string {
	$string = $str;
	$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´");
	$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "");
	while(current($search)) {
		$string = preg_replace('/'.utf8_encode(current($search)).'/', $replace[key($search)], $string);
		next($search);
	}
	return $string;
}

function getURL($int) {
	$url = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	if(!key_exists($int, $url)) return false;
	return $url[$int];
}

function checkURL($int, $search) {
	if(getURL($int) == $search) return true;
	return false;
}