<?php
if(!isset($_POST)) returnErrorJSON('No Data given!');

$starttime = createTime($_POST['start']);
$endtime = createTime($_POST['end']);

if($starttime >= $endtime) returnErrorJSON(getString('calendaradmin invalidTime'));

if(intval($_POST['day']) > 7 || intval($_POST['day']) < 0) returnErrorJSON(getString('calendaradmin wrongDay'));
if(!is_int(($starttime%60) / 15) || !is_int(($endtime%60) / 15)) returnErrorJSON(getString('calendaradmin invalidMin'));

global $mysql, $user;

$mysql->where('cid', intval($_POST['cid']));
$mysql->where('start', $starttime, "<=");
$mysql->where('end', $starttime, ">");
$mysql->where('day', intval($_POST['day']));
$mysql->select('pattern');

if($mysql->countResult() > 0) returnErrorJSON(getString('calendaradmin timeBlocked'));

$mysql->where('calendar.cid', $_POST['cid']);
$mysql->join(array("calendar" => 'vsid', "versammlungen" => "vsid"));
$mysql->select("calendar", array('versammlungen.vsid' => "vsid"), 1);

if($mysql->countResult() != 1) returnErrorJSON(getString('errors noPerm'));
if($mysql->fetchRow()->vsid != $user->vsid) returnErrorJSON(getString('errors noPerm'));

$insertdata = array(
		"start" => $starttime,
		"end" => $endtime,
		"cid" => intval($_POST['cid']),
		"day" => intval($_POST['day']),
		"count" => intval($_POST['count'])
);

if(!$mysql->insert('pattern', $insertdata)) returnErrorJSON(getString('errors sql'));