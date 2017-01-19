<?php
function getString($tree) {
	global $Lang;
	return $Lang->getValue($tree);
}

function displayString($tree) {
	echo getString($tree);
}

function returnErrorJSON($string) {
	echo json_encode(array('error' => $string));
	exit;
}
function isLoggedIn() {
	global $User;
	return $User->IsLoggedIn;
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