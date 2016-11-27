<?php
global $user, $mysql;
/*
 * Daten testen
 */

$name = $_POST['name'];
$uid = $_POST['uid'];
$password = $_POST['password'] == '' ? '' : hash('sha512', $_POST['password'].SALT);
$email = $_POST['email'];
$active = intval($_POST['active']);
$vsid = empty($_POST['vsid']) ? null : $_POST['vsid'];
$perms = empty($_POST['perms']) ? array() : $_POST['perms'];
$groups = empty($_POST['groups']) ? array() : $_POST['groups'];

if(empty($_POST['name']) || ($active !== 0 && $active != 1) || empty($_POST['vsid']))
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
 * Permissions sortieren, Sonderpermissions aussortieren, permstring bilden, Alte VS rausfinden (fürs update der Gruppen)
 */
		
$permarray = array();

$mysql->where('uid', $uid);
$mysql->select('users', array('perms', 'vsid'), 1);
$olddata = $mysql->fetchRow(true);
$oldperms = json_decode($olddata['perms']);
$oldvs = $olddata['vsid'];

while(current($oldperms)) {
	//Sonderpermission automatisch mit übernehmen
	if(strpos(current($oldperms), '.vs.') !== false) $permarray = current($oldperms);
	next($oldperms);
}
reset($oldperms);

while(current($perms)) {
	//Alle übergebenen Permissions übernehmen
	$permarray[] = current($perms);
	next($perms);
}

/*
 * Benutzerdaten updaten
 */

$updateArray = array(
		"name" => $name,
		"email" => $email,
		"active" => $active,
		"vsid" => $vsid,
		"perms" => json_encode($permarray)
);

if($password != '') $updateArray['password'] = $password;

$mysql->where('uid', $uid);
if(!$mysql->update('users', $updateArray)) returnErrorJSON(getString('errors sql'));

/*
 * Gruppen updaten
 */

$mysql->where('vsid', $oldvs);
$mysql->where('vsid', $vsid, '=', 'OR');
$mysql->select('groups');

$groups2check = $mysql->fetchAll();
foreach($groups2check AS $currGrp)
	if(strpos($currGrp['members'], $uid) && !in_array($currGrp['gid'], $groups)) {
		$members = json_decode($currGrp['members']);
		unset($members[array_search($uid, $members)]);
		$members = array_values($members);
		
		$mysql->where('gid', $currGrp['gid']);		
		if(!$mysql->update('groups', array('members' => json_encode($members)))) returnErrorJSON(getString('errors sql'));
		
	} elseif(in_array($currGrp['gid'], $groups) && strpos($currGrp['members'], $uid) === false) {
		$members = json_decode($currGrp['members']);
		$members[] = $uid;
		$members = array_values($members);
		
		$mysql->where('gid', $currGrp['gid']);
		if(!$mysql->update('groups', array('members' => json_encode($members)))) returnErrorJSON(getString('errors sql'));
		
	}

