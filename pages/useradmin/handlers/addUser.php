<?php
global $user, $mysql;
/*
 * Daten testen
 */

$name = $_POST['name'];
$password = hash('sha512', $_POST['password'].SALT);
$email = $_POST['email'];
$active = intval($_POST['active']);
$vsid = empty($_POST['vsid']) ? null : $_POST['vsid'];
$perms = empty($_POST['perms']) ? array() : $_POST['perms'];
$groups = empty($_POST['groups']) ? array() : $_POST['groups'];

if(empty($_POST['name']) || empty($_POST['password']) || ($active !== 0 && $active != 1) || empty($_POST['vsid']))
	returnErrorJSON(getString('errors FormfillError'));
		
/*
 * Recht prüfen
 */

if(!array_key_exists($vsid, getVSAccess('useredit')))
	returnErrorJSON(getString('errors noPerm'));

foreach($perms AS $perm)
	if(!$user->hasPerm($perm))
		returnErrorJSON(getString('errors noPerm'));

foreach($groups AS $group)
	$mysql->where('gid', $group, '=', 'OR');

$mysql->select('groups', array('vsid'));

foreach($mysql->fetchAll() AS $currVsId)
	if(!array_key_exists($currVsId['vsid'], getVSAccess('useredit')))
		returnErrorJSON(getString('errors noPerm'));

/*
 * UID erstellen
 */		

$username = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
$username = str_replace('ä', 'ae', $username);
$username = str_replace('ö', 'oe', $username);
$username = str_replace('ü', 'ue', $username);
$username = str_replace('ß', 'ss', $username);
$username = preg_replace('/[^A-Za-z\-]/', '', $username);
$username = strtolower($username);

//UID prüfen
$counter = 2;
$newUID = $username;
while(true) {
	$mysql->where('uid', $newUID);
	$mysql->select('users', array('uid'));
	
	if($mysql->countResult() > 0) {
		$newUID = $username."-".$counter;
		$counter++;
	} else {
		$username = $newUID;
		break;
	}
}
if(substr($username, -1, 1) == '-') $username = substr($username, 0, strlen($username) - 1);

/*
 * Daten eintragen
 */
	
if(!$mysql->insert('users', array(
		"uid" => $username,
		"name" => $name,
		"password" => $password,
		"email" => $email,
		"active" => $active,
		"vsid" => $vsid,
		"perms" => json_encode($perms)
	))) returnErrorJSON(getString('errors sql'));

/*
 * Gruppen aktualisieren
 */

$groupsForUpdate = array();
$groupsForQuery = array();

foreach($groups AS $group)
	$mysql->where('gid', $group, '=', 'OR');

$mysql->select('groups', array('gid', 'members'));

foreach($mysql->fetchAll() AS $currGroup)
	$groupsForUpdate[$currGroup['gid']] = json_decode($currGroup['members']);

foreach($groups AS $group)
	$groupsForUpdate[$group][] = $username;

foreach($groupsForUpdate AS $gid => $toJSON) {
	$mysql->where('gid', $gid);
	if(!$mysql->update('groups', array('members' => json_encode($toJSON)))) returnErrorJSON(getString('errors sql'));
}


?>