<?php
require_once 'oop/language.php';
require_once 'libs/mysql.php';

$lang = new language();
$lang->display('menu->calendar');
echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
?>