<?php
if(!isset($_POST['pid']) || !isset($_POST['uid'])) exit;

global $mysql, $user;

if(!$user->hasPerm('calender.entry.other')) exit;

$mysql->where('pid', $_POST['pid']);
$mysql->join(array('posts' => 'cid', 'calendar' => 'cid'));
$mysql->select('posts', array('entrys', 'calendar.vsid' => 'vsid'), 1);

if($mysql->countResult() == 0) exit;

$result = $mysql->fetchRow();
if($result->vsid != $user->vsid) exit;

$entrys = json_decode($result->entrys);

unset($entrys[array_search($_POST['uid'], $entrys)]);

$entrys = array_values($entrys);

$mysql->where('pid', $_POST['pid']);
if(!$mysql->update('posts', array('entrys' => json_encode($entrys)))) returnErrorJSON(getString('errors sql'));