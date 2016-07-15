<?php
session_start();
header ('Content-type: text/html; charset=utf-8');
require_once 'config.php';
require_once 'oop/language.php';
require_once 'oop/mysql.php';
require_once 'oop/user.php';
require_once 'oop/urlpath.php';
require_once 'libs/functions.php';
require_once 'oop/log.php';


require_once 'libs/auth.php';




require_once 'sites/header.php';
switch ($url->value(0)) {
	case 'logout':
		require_once 'sites/logout.php';
		break;
	case 'calendar':
		require_once 'sites/calendar.php';
		break;
	case 'profile':
		echo "Profil";
		break;
	case 'admin':
		require_once 'sites/admin.php';
		break;
	case 'sytem':
		echo "System";
		break;
	case 'login':
		require_once 'sites/login.php';
		break;
	default:
		break;
}
require_once 'sites/footer.php';
?>