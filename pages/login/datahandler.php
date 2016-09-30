<?php
if(!isset($_POST['username']) || !isset($_POST['password']))
	returnErrorJSON(getString("errors FormfillError"));

global $mysql;
$username = $_POST['username'];
$password = hash('sha512', $_POST['password']);

$mysql->where('uid', $username);
$mysql->where('email', $username, '=', 'OR');
$mysql->select('users', array('active', 'password', 'uid'), 1);
$result = $mysql->fetchRow();

if(!$result) //Abbruch wenn Benutzer nicht gefunden
	returnErrorJSON(getString("errors invalidAuth"));

if($result->active != 1) //Wenn Benutzer nicht aktiv
	returnErrorJSON(getString("errors accountInactive"));

if($result->password != $password) //Passwort falsch
	returnErrorJSON(getString("errors invalidAuth"));

$mysql->insert('sessions', array('sid' => session_id(), 'uid' => strval($result->uid), 'expire' => time() + (60*SESSIONTIME)));


?>