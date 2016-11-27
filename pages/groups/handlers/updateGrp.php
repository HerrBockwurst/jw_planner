<?php
if(!isset($_POST)) exit;

global $mysql, $user;

$mysql->where('gid', $_POST['gid']);
$mysql->select('groups', null, 1);

if($mysql->countResult() == 0) returnErrorJSON(getString('groups gidNotFound'));
$group = $mysql->fetchRow();

if($group->vsid != $user->vsid) returnErrorJSON(getString('errors noPerm'));

$users = isset($_POST['users']) ? $_POST['users'] : array();
$users = array_values($users);

$mysql->where('gid', $_POST['gid']);
if(!$mysql->update('groups', array('members' => json_encode($users)))) returnErrorJSON(getString('errors sql'));

echo json_encode(array('success' => getString('groups updateSuccess')));