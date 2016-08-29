<?php
if(!defined('index')) exit;
$MAILS = array();

define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'jwplanner');
define('MYSQL_PASSWORD', '6TPhHjEYZQCmyEKz');
define('MYSQL_DATABASE', 'jwplanner_rewrite');

define('SSL', false);
define('HOME', 'jw.herrbockwurst.de');
define('TITLE', 'JWPlanner');
define('VERSION', 'PREALPHA 0.1');
define('SESSIONTIME', 20);
define('MAXPERS', 100);

/*
 * Für Bob
 */
define('MODUL', 1);
define('PAGE', 2);
define('DIRECT', 3);

$MAILS['contact'] = 'contact@jwplanner.com';
$MAILS['error'] = 'bugreport@jwplanner.com';

