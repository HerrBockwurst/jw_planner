<?php
if(!isset($_POST['pid'])) exit;

global $mysql, $user;

if(!$user->hasPerm('admin.calendar')) exit;

$mysql->where('pid', $_POST['pid']);
$mysql->join(array('posts' => 'cid', 'calendar' => 'cid'));
$mysql->select('posts', array('calendar.vsid' => 'vsid'), 1);

if($mysql->countResult() == 0) exit;

$result = $mysql->fetchRow();
if($result->vsid != $user->vsid) exit;

$mysql->where('pid', $_POST['pid']);
if(!$mysql->delete('posts')) returnErrorJSON(getString('errors sql'));