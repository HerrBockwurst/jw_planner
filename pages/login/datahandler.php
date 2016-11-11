<?php
if(!isset($_POST['username']) || !isset($_POST['password']))
	returnErrorJSON(getString("errors FormfillError"));

global $mysql;
$username = $_POST['username'];
$password = hash('sha512', $_POST['password']);

/*
 * Loginfails
 */

$mysql->where('time', time() - 60*BANTIME, '<=');
if(!$mysql->delete('loginfails')) returnErrorJSON(getString('errors sql'));

$mysql->where('ip', $_SERVER['REMOTE_ADDR']);
$mysql->select('loginfails', array('ip'));
if($mysql->countResult() > LOGINTRY) returnErrorJSON(getString('errors banned'));

/*
 * Login
 */

$mysql->where('uid', $username);
$mysql->where('email', $username, '=', 'OR');
$mysql->select('users', array('active', 'password', 'uid'), 1);
$result = $mysql->fetchRow();

if(!$result) {//Abbruch wenn Benutzer nicht gefunden
	$mysql->insert('loginfails', array('ip' => $_SERVER['REMOTE_ADDR'], 'time' => time(), 'user' => $username));
	returnErrorJSON(getString("errors invalidAuth"));	
}
if($result->active != 1) //Wenn Benutzer nicht aktiv
	returnErrorJSON(getString("errors accountInactive"));

if($result->password != $password) {//Passwort falsch
	$mysql->insert('loginfails', array('ip' => $_SERVER['REMOTE_ADDR'], 'time' => time(), 'user' => $username));
	returnErrorJSON(getString("errors invalidAuth"));
}

$mysql->insert('sessions', array('sid' => session_id(), 'uid' => strval($result->uid), 'expire' => time() + (60*SESSIONTIME)));

?>