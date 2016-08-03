<?php

if(!defined('index')) exit;
global $user, $mysql;

if($_POST['username'] == '' && $_POST['versammlung'] == ''):
	$data = array('error' => getString('errors>usersearch_empty'));
	echo json_encode($data);
	exit;
endif;

/*
 * Liste der zu durchsuchenden Versammlungen erstellen
 * 1. Spezielle Permissions testen (z.B. admin.usersearch.stollber-erzgeb
 * Wenn keine vorhanden, dann wird nur die eigenen versammlung durchsucht
 * Wildcard * wird erkannt
 */
$versammlungen = $user->getSubPerm('admin.usersearch');

if(!$versammlungen) $versammlungen = array($user->vsid);

foreach($versammlungen AS $vs) {
	/*
	 * Falls Wildcard gesetzt wurde, wird die Variable wildcard auf true gesetzt (Weil MySQL Abfrage anders ist)
	 */
	if(strpos($vs, '*') !== false):
		$result = $mysql->execute("SELECT u.uid, u.name, u.vsid, v.name AS vname
				FROM user AS u
				INNER JOIN versammlungen AS v ON (u.vsid = v.vsid)
				WHERE u.uid LIKE ?",
				's', '%'.$_POST['username'].'%');
		/*
		 * Wenn keine Daten vorhanden, break;
		 */
		if($result->num_rows == 0):
			$data = array('error' => getString('errors>usersearch_noUserFound'));
			echo json_encode($data);
			break;
		endif;
		
		$result = $result->fetch_all(MYSQLI_ASSOC);
		echo json_encode($result);
		break;
			
	else:
	endif;
	
}


$mysql->execute("SELECT u.uid, u.name, u.vsid, v.name AS vname
				FROM user AS u
				INNER JOIN versammlungen AS v ON (u.vsid = v.vsid)
				WHERE u.uid LIKE ?",
				's', '%'.$_POST['username'].'%');
$mysql->execute("SELECT u.uid, u.name, u.vsid, v.name AS vname FROM user AS u INNER JOIN versammlungen AS v ON (u.vsid = v.vsid) WHERE v.name LIKE ?", 's', '%'.$_POST['versammlung'].'%');
?>
