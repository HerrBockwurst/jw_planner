<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.admin')) exit;

/*
 * Berechtigung prüfen
 */

if(!isset($_POST['pid'])) exit;

$result = $mysql->execute("SELECT c.vsid FROM calendar AS c INNER JOIN posts AS p ON (p.cid=c.cid) WHERE p.pid = ? LIMIT 1", 'i', $_POST['pid']);

if($result->num_rows != 1):
	$data = array("error" => getString('errors>pid_not_found'));
	echo json_encode($data);
	exit;
endif;

$result = $result->fetch_assoc();

if(!array_key_exists($result['vsid'], getVSArray())):
	$data = array("error" => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;

/*
 * Alles OK, löschen
 */

if(!$mysql->execute('DELETE FROM posts WHERE pid = ?', 'i', $_POST['pid'])):
	$data = array("error" => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

if(!$mysql->execute('DELETE FROM entrys WHERE pid = ?', 'i', $_POST['pid'])):
	$data = array("error" => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$data = array('success' => getString('admin>post_deleted'));
echo json_encode($data);
exit;