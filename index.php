<?php
session_start();
require_once 'config.php';
require_once 'oop/language.php';
require_once 'oop/mysql.php';
require_once 'oop/urlpath.php';
require_once 'libs/functions.php';
// Blau 4a6da7

//printURL();
$result = $mysql->doQuery("SELECT * FROM `config` WHERE `conf` = 'url' LIMIT 1", true);
/*
$mysql = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DATABASE);
$result = $mysql->query("SELECT * FROM `config` WHERE `conf` = 'url' LIMIT 1");
var_dump($result);
echo $mysql->error;
*/

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