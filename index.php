<?php
session_start();
require_once 'config.php';
require_once 'oop/language.php';
require_once 'libs/mysql.php';
require_once 'oop/urlpath.php';

switch ($url->value(0)) {
	case 'calendar':
		echo "Kalender";
		break;
	case 'profile':
		echo "Profil";
		break;
	default:
		echo "Kalender";
		break;
}
?>