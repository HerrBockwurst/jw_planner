<?php
if(!defined('index')) exit;
global $mysql, $user;

/*
 * Benutzer abfragen
 */

$result = $mysql->execute("SELECT * FROM user WHERE uid = ? LIMIT 1", 's', $_POST['uid']);

if($result->num_rows != 1):
	$data = array('error' => getString('errors>usersearch_noUserFound'));
	echo json_encode($data);
	exit;
endif;

$result = $result->fetch_assoc();

/*
 * Versammlung abfragen + Prüfen ob Benutzer Berechtigung für die Versammlung hat
 */

if(!array_key_exists($result['vsid'], getVSArray())):
	$data = array('error' => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;

$data = array("uid" => $result['uid'], "name" => $result['name'], "vsid" => $result['vsid'], "email" => $result['email'], "active" => $result['active']);

/*
 * Permissions abfragen
 */

$result = $mysql->execute("SELECT perms FROM permissions WHERE uid = ? LIMIT 1", 's', $data['uid']);

if($result->num_rows != 1):
	$data = array('error' => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$result = $result->fetch_assoc();
$data['perms'] = json_decode($result['perms']);

echo json_encode($data);
exit;
?>