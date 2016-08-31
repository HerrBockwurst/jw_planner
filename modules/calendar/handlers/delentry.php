<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.entry')) exit;

if(!isset($_POST['eid'])) exit;

$result = $mysql->execute("SELECT uid, pid FROM entrys WHERE eid = ? LIMIT 1", 'i', $_POST['eid']);
if($result->num_rows == 0):
	echo json_encode(array("error" => getString('errors>entry_not_found')));
	exit;
endif;

$result = $result->fetch_assoc();

if($result['uid'] != $user->uid && !$user->hasPerm('calendar.admin')):
	echo json_encode(array("error" => getString('errors>noPerm')));
	exit;
endif;

if(!$mysql->execute("DELETE FROM entrys WHERE eid = ?", 'i', $_POST['eid'])):
	echo json_encode(array("error" => getString('errors>MySQL')));
	exit;
endif;

$count = $mysql->execute("SELECT eid FROM entrys WHERE pid = ?", 'i', $result['pid']); 

echo json_encode(array("success" => getString('calendar>entry_successful'), "pid" => $result['pid'], "newcount" => $count->num_rows, "tooltip" => getTooltip($result['pid'])));