<?php
session_start();
$index = true;
header ('Content-type: text/html; charset=utf-8');

require_once 'libs/functions.php';
require_once 'config.php';
require_once 'oop/mysql.php';
require_once 'oop/language.php';
require_once 'oop/modules.php';
require_once 'oop/bob.php';


/*
 * Ab hier nur weiter, wenn Daten über JavaScript geladen werden
 */
if(!checkURL(0, 'ajax')) exit;

require_once 'oop/user.php';

if(checkURL(0, 'ajax')) require_once 'ajax/index.php';