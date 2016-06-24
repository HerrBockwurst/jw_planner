<?php

while(true):
/*
 * Testet ob Benutzer die Berechtigung für Versammlung hat
 */

if($_POST['versammlung'] != $USER->versammlung &&
	!$USER->hasPerm('admin.useredit.vs.'.$_POST['versammlung']) &&
	!$USER->hasPerm('admin.useredit.global')):
	$ERROR['useradd'] = getLang('errors>noperm');
	break;
endif;


/*
 * Teste Permissions
 */
$perms = array();
foreach($_POST AS $key => $value):
	if(strpos($key, 'permission') !== false) $perms[] = str_replace('_', '.', substr($key, 11));
	if(strpos($key, 'permission') !== false && !$USER->hasPerm(str_replace('_', '.', substr($key, 11)))):
		$ERROR['useradd'] = getLang('errors>noperm');
		break 2;
	endif;	
endforeach;

/*
 * Teste auf alles ausgefüllt
 */

if($_POST['name'] == "" || $_POST['password'] == "" || $_POST['password2'] == "" || $_POST['versammlung'] == ""):
	$ERROR['useradd'] = getLang('errors>emptyfields');
	break;
endif;

/*
 * Teste ob Passwörter überein stimmen
 */

if(utf8_decode($_POST['password']) != utf8_decode($_POST['password2'])):
	$ERROR['useradd'] = getLang('errors>passwordnomatch');
	break;
endif;

/*
 * Alles OK, eintragen
 * Passwort Hashen und Username erstellen
 */

$cleanpw = utf8_encode($_POST['password']);
$password = hash('sha512', utf8_decode($_POST['password']));
$username = str_replace(' ', '-', utf8_decode($_POST['name'])); // Replaces all spaces with hyphens.
$username = str_replace('ä', 'ae', $username);
$username = str_replace('ö', 'oe', $username);
$username = str_replace('ü', 'ue', $username);
$username = str_replace('ß', 'ss', $username);
$username = preg_replace('/[^A-Za-z\-]/', '', $username);
$username = strtolower($username);

/*
 * Teste ob Username schon vergeben
 */

$result = $mysql->execute("SELECT `uid` FROM `users` WHERE `uid` LIKE ?", 's', "%".$username."%");
$rows = $result->fetch_all(MYSQLI_ASSOC);

$userlist = array();
foreach($rows AS $row) $userlist[] = $row['uid'];

$newusername = "";
$i = 0;
while(true):
	if($i != 0) $newusername = $username."-".$i;
	else $newusername = $username;
	if(!in_array($newusername, $userlist)) break;
	$i++;
endwhile;
$username = $newusername;

/*
 * Array für Datenübergabe an Datenbank vorbereiten
 */

$inserdata = array();
$inserdata[] = $username;
$inserdata[] = $_POST['name'];
$inserdata[] = $password;

//Expire Datum
if(isset($_POST['noexpire'])) $inserdata[] = getSQLDate(PHP_INT_MAX);
else $inserdata[] = getSQLDate();

$inserdata[] = $_POST['versammlung'];

if(isset($_POST['email'])) $inserdata[] = $_POST['email'];
else $inserdata[] = "";

if(isset($_POST['active'])) $inserdata[] = "active";
else $inserdata[] = "inactive";

$inserdata[] = getSQLDate();

$result = $mysql->execute("INSERT INTO `users` (`uid`, `name`, `password`, `p_eval`, `versammlung`, `email`, `status`, `created`)
						VALUES (?,?,?,?,?,?,?,?)", 'ssssssss', $inserdata);
if($result != true):
	$log->write('Konnte Benutzer nicht anlegen: '.$mysql->error(), 'error');
	$ERROR['useradd'] = getLang('errors>mysql');
	break;
endif;

/*
 * Benutzer ist angelegt, hier jetzt Berechtigungen anlegen.
 */

foreach($perms AS $perm):	
	$insertdata = array($username, $perm); 
	$result = $mysql->execute("INSERT INTO `permissions` (`uid`,`perm`) VALUES (?,?)", 'ss', $insertdata);
	if($result != true):
		$log->write("Konnte Permissions nicht eintragen: ".$mysql->error(), 'error');
		break 2;
	endif;
endforeach;

$log->write("Benutzer ".$username." erfolgreich angelegt.");
$SUCCESS['useradd'] = array($username, $password);

break;
endwhile;
?>