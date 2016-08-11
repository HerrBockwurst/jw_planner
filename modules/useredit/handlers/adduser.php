<?php
global $mysql, $user;

$perms = array();
if(isset($_POST['perms'])) $perms = $_POST['perms'];

/*
 * Permission prüfen
 */

foreach($perms AS $perm):
	if(!$user->hasPerm($perm)):
		$data = array('error' => getString('errors>noPerm'));
		echo json_encode($data);
		exit;
	endif;
endforeach;

/*
 * Passwörter prüfen
 */
if($_POST['pw1'] != $_POST['pw2']): 
	$data = array("error" => getString('errors>passwordNoMatch'));
	echo json_encode($data);
	exit;
endif;

$password = hash('sha512', $_POST['pw1']);

/*
 * Benutzernamen erstellen
 */

$username = str_replace(' ', '-', $_POST['name']); // Replaces all spaces with hyphens.
$username = str_replace('ä', 'ae', $username);
$username = str_replace('ö', 'oe', $username);
$username = str_replace('ü', 'ue', $username);
$username = str_replace('ß', 'ss', $username);
$username = preg_replace('/[^A-Za-z\-]/', '', $username);
$username = strtolower($username);

/*
 * prüfen ob benutzename vergeben wurde
 */

$result = $mysql->execute("SELECT uid FROM user WHERE uid LIKE ?", 's', "%".$username."%");

if($result->num_rows != 0):
	$newres = array();
	$result = $result->fetch_all(MYSQLI_ASSOC);
	foreach ($result AS $cres):
		$newres[] = $cres['uid'];
	endforeach;
	
	$count = 1;
	$newusername = $username;
	while(in_array($newusername, $newres)):
		$newusername = $username."-".$count;
		$count++;
	endwhile;
	$username = $newusername;
endif;

$name = $_POST['name'];

/*
 * Aktiv?
 */
$active = 'inactive';
if($_POST['active'] == 1) $active = 'active';

/*
 * Email
 */
$email = $_POST['email'];

if($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)):
	$data = array('error' => getString('errors>invalidEmail'));
	echo json_encode($data);
	exit;
endif;

/*
 * Versammlung
 */

$vs = getVSArray();

if(!isset($vs[$_POST['vs']])):
	$data = array('error' => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;
$insertdata = array($username, $name, $_POST['vs'], $password, $email, $active);

/*
 * Benutzer erstellen
 */

if(!$mysql->execute("INSERT INTO user(uid, name, vsid, password, email, active) VALUES (?,?,?,?,?,?)", 'ssssss', $insertdata)):
	$data = array('error' => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

/*
 * Permissions eintragen
 */

$insertdata = array($username, json_encode($perms));

if(!$mysql->execute("INSERT INTO permissions(uid, perms) VALUES (?,?)", 'ss', $insertdata)):
	$data = array('error' => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$data = array('success' => true, 'username' => $username, 'password' => $_POST['pw1']);
echo json_encode($data);
exit;