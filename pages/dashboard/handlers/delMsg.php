<?php
global $user, $mysql;
if(!isset($_POST['msg_id']) || ($_POST['msg_id']) == '') exit;

$id = intval($_POST['msg_id']);

$mysql->where('msg_id', $id);
$mysql->select('messages', array('sender'));
if($mysql->countResult() == 0) exit;

if(!$user->hasPerm('dashboard.admin') && $user->uid != $mysql->fetchRow()->sender) returnErrorJSON(getString('errors noPerm'));

$mysql->where('msg_id', $id);
if(!$mysql->delete('messages')) returnErrorJSON(getString('errors sql'));
echo json_encode(array());