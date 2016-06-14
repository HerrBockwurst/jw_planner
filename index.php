<?php
require_once 'oop/language.php';
require_once 'libs/mysql.php';


$lang->display('menu>calendar');
$lang->display('menu>profile');
echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
?>