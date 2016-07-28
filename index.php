<?php
session_start();
define('index', true);
header ('Content-type: text/html; charset=utf-8');

require_once 'libs/functions.php';

if(checkURL(0, 'ajax')) define('ajax', true);
if(checkURL(1, 'datahandler')) define('datahandler', true);

require_once 'config.php';
require_once 'oop/mysql.php';
require_once 'oop/language.php';
require_once 'oop/modules.php';
require_once 'oop/bob.php';

if(defined('ajax')) require_once 'oop/user.php';

if(defined('ajax')) require_once 'ajax/index.php';

$bob->buildFooter();