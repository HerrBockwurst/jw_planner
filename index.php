<?php
session_start();
require_once 'config.php';
require_once 'oop/language.php';
require_once 'oop/mysql.php';
require_once 'libs/sysvars.php';
require_once 'oop/urlpath.php';
require_once 'libs/functions.php';


require_once 'libs/auth.php';


require_once 'sites/header.php';
switch ($url->value(0)) {
	case 'calendar':
		require_once 'sites/calendar.php';
		break;
	case 'profile':
		echo "Profil";
		break;
	case 'admin':
		echo "Admin";
		break;
	case 'system':
		echo "System";
		break;
	default:
		echo "Default";
		break;
}
require_once 'sites/footer.php';
?>