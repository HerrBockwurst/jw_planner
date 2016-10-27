<?php
if(!isset($_POST['uid']))
	returnErrorJSON('No UID given!');

global $mysql, $user;

$mysql->where('uid', $_POST['uid']);
$mysql->select('users', null, 1);

if($mysql->countResult() == 0)
	returnErrorJSON(getString('errors noUsersFound'));

$result = $mysql->fetchRow(true);

if(!array_key_exists($result['vsid'], getVSAccess('useredit')))
	returnErrorJSON(getString('errors noPerm'));

/*
 * Gruppen Auslesen, wo der Nutzer drin ist
 */
	
$mysql->where('vsid', $result['vsid']);
$mysql->select('groups');
$gresult = $mysql->fetchAll();

$groups = array();

foreach($gresult AS $currResult) {
	$cMembers = json_decode($currResult['members']);
	if(in_array($result['uid'], $cMembers))
		$groups[] = $currResult['gid'];
}

$result['groups'] = $groups;

echo json_encode($result);