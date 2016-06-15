<?php
session_start();
require_once 'config.php';
require_once 'oop/language.php';
require_once 'oop/mysql.php';
require_once 'libs/sysvars.php';
require_once 'oop/urlpath.php';
require_once 'libs/functions.php';



switch ($url->value(0)) {
	case 'calendar':
		echo "Kalender";
		break;
	case 'profile':
		echo "Profil";
		break;
	default:
		require_once 'sites/header.php';
		break;
}
?>