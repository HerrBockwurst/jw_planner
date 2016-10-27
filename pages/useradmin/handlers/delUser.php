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
$mysql->where('members', '%\"'.$result['uid'].'\"%', 'LIKE', 'AND');
$mysql->select('groups');
$gresult = $mysql->fetchAll();

/*
 * Benutzer l�schen
 */

$mysql->where('uid', $result['uid']);
if(!$mysql->delete('users')) returnErrorJSON(getString('errors sql'));

/*
 * Benutzer aus Gruppen entfernen
 */
print_r($gresult);
foreach($gresult AS $currG) {
	$members = json_decode($currG['members']);
	unset($members[array_search($result['uid'], $members)]);
	
	$mysql->where('gid', $currG['gid']);
	if(!$mysql->update('groups', array('members' => json_encode($members)))) returnErrorJSON(getString('errors sql'));
}	

/*
 * Benutzer aus Posts l�schen
 * TODO
 */