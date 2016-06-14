<?php
session_start();
require_once 'config.php';
require_once 'oop/language.php';
require_once 'oop/mysql.php';
require_once 'oop/urlpath.php';
require_once 'libs/functions.php';
// Blau 4a6da7

printURL();

switch ($url->value(0)) {
	case 'calendar':
		echo "Kalender";
		break;
	case 'profile':
		echo "Profil";
		break;
	default:
		//require '';
		break;
}
?>