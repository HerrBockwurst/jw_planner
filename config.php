<?php
//if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;
$CONFIG = array(); //Init Array
$ERROR = array(); //Init Error Array
$SUCCESS = array(); //Init Success Array

$CONFIG['MYSQL_HOST'] = 'localhost';
$CONFIG['MYSQL_USER'] = 'jwplanner';
$CONFIG['MYSQL_PASSWORD'] = '6TPhHjEYZQCmyEKz';
$CONFIG['MYSQL_DATABASE'] = 'jwplanner';

$CONFIG['ssl'] = false;
$CONFIG['home'] = 'jw.herrbockwurst.de';
$CONFIG['title'] = 'JW Planner';
$CONFIG['version'] = 'PREALPHA 0.1';
$CONFIG['version_count'] = '16w28';
$CONFIG['contactmail'] = 'contact@jwplanner.com';
$CONFIG['errormail'] = 'bugreport@jwplanner.com';
$CONFIG['sessiontime'] = 20;


?>