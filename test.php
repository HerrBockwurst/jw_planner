<?php
/*
require_once 'config.php';
require_once 'oop/mysql.php';

$mysql->select('users');
$users = $mysql->fetchAll();

foreach($users AS $user) {
	$perms = json_decode($user['perms']);
	if(in_array('dashboard.msg', $perms)) continue;
	
	$perms[] = 'dashboard.msg';
	
	$mysql->where('uid', $user['uid']);
	$mysql->update('users', array('perms' => json_encode($perms)));
}
*/