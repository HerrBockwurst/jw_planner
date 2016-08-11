<?php
global $mysql, $user;
$perms = array();
if(isset($_POST['perms'])) $perms = $_POST['perms'];

/*
 * User auslesen
 */

$result = $mysql->execute("SELECT u.*, p.perms FROM user AS u INNER JOIN permissions AS p ON (u.uid=p.uid) WHERE u.uid = ? LIMIT 1", "s", $_POST['uid']);
if($result->num_rows != 1):
	$data = array("error" => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$result = $result->fetch_assoc();

$eperms = json_decode($result['perms']);

/*
 * Prüfen ob Benutzer die Berechtigung für die VS hat
 */

if(!array_key_exists($result['vsid'], getVSArray())):
	$data = array("error" => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;

if($_POST['del'] == 1):
	if(!$mysql->execute("DELETE FROM user WHERE uid = ?", 's', $_POST['uid'])):
		$data = array('error' => getString('errors>MySQL'));
		json_encode($data);
		exit;
	endif;
	
	if(!$mysql->execute("DELETE FROM permissions WHERE uid = ?", 's', $_POST['uid'])):
		$data = array('error' => getString('errors>MySQL'));
		json_encode($data);
		exit;
	endif;
	
	//TODO Kalendereinträge löschen

	$data = array('success' => getString('admin>userDeleted'), 'deleted' => 1);
	echo json_encode($data);
	exit;
endif;

/*
 * Passwort Test
 */

$p1 = hash('sha512', $_POST['p1']);
$p2 = hash('sha512', $_POST['p2']);

if($p1 != $p2):
	$data = array("error" => getString('errors>passwordNoMatch'));
	echo json_encode($data);
	exit;
endif;

$nopw = false;
if($_POST['p1'] != '') $nopw = true;

/*
 * Email Test
 */

$email = $_POST['email'];

if($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)):
	$data = array('error' => getString('errors>invalidEmail'));
	echo json_encode($data);
	exit;
endif;

/*
 * Aktiv?
 */
$active = 'inactive';
if($_POST['active'] == 1) $active = 'active';

/*
 * Permissionänderungen feststellen
 */

foreach($eperms AS $cperm):
	if(!in_array($cperm, $user->getPerms())):
		/*
		 * Bei manipulierten Formular. Übergebene Permissions werden geprüft ob der Bearbeiter die Permissions hat
		 */
		$data = array("error" => getString('errors>noPerm'));
		echo json_encode($data);
		exit;
	endif;
	
	/*
	 * Nichts manipuliert, ab hier geprüft was gelöscht werden muss und was nicht
	 */
	
	if(!in_array($cperm, $perms) && in_array($cperm, $eperms)) unset($eperms[array_search($cperm, $eperms)]);
endforeach;

foreach($perms AS $cperm):
	if(!in_array($cperm, $user->getPerms())):
		/*
		 * Bei manipulierten Formular. Übergebene Permissions werden geprüft ob der Bearbeiter die Permissions hat
		 */
		$data = array("error" => getString('errors>noPerm'));
		echo json_encode($data);
		exit;
	endif;
	
	/*
	 * Nichts manipuliert, ab hier geprüft was gelöscht werden muss und was nicht
	 */
	
	if(!in_array($cperm, $eperms) && !in_array($cperm, $eperms)) $eperms[] = $cperm;
endforeach;

/*
 * Benutzer updaten
 */

$qry = "UPDATE user SET name = ?, vsid = ?, email = ?, active = ?";
if(!$nopw) $qry .= ", password = ?";
$qry .= " WHERE uid = ?";

$s = 'sssss';
if(!$nopw) $s .= 's';

$udata = array($_POST['name'], $_POST['vs'], $email, $active);
if(!$nopw) $udata[] = $p1;
$udata[] = $_POST['uid'];

if(!$mysql->execute($qry, $s, $udata)):
	$data = array('error' => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

if(!$mysql->execute("UPDATE permissions SET perms = ? WHERE uid = ?", 'ss', array(json_encode(array_values($eperms)), $_POST['uid']))):
	$data = array('error' => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$data = array('success' => getString('admin>userUpdated'));
echo json_encode($data);
exit;
?>