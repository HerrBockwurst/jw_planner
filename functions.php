<?php
function getURL($int) {
	$url = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	if(!key_exists($int, $url)) return false;
	return $url[$int];
}

function getString($tree) {
	return Language::getInstance()->getValue($tree);
}

function displayString($tree) {
	echo getString($tree);
}