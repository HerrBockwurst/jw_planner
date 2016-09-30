<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('system.query')) exit;

$result = $mysql->execute($_POST['qry']);

if($_POST['step2'] == 'fetched') $result = $result->fetch_all(MYSQLI_ASSOC);
?><pre><?php
if($_POST['step3'] == 'dump') var_dump($result);
else print_r($result);
?></pre>