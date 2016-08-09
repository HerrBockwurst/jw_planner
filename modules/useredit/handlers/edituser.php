<?php
global $mysql, $user;

/*
 * Benutzer abfragen
 */

$result = $mysql->execute("SELECT * FROM user WHERE uid = ? LIMIT 1", 's', $_POST['uid']);

if($result->num_rows != 1):
	$data = array('error' => getString('errors>usersearch_noUserFound'));
	echo json_encode($data);
	exit;
endif;

$result = $result->fetch_assoc();

/*
 * Versammlung abfragen + Prüfen ob Benutzer Berechtigung für die Versammlung hat
 */

if(!array_key_exists($result['vsid'], getVSArray())):
	$data = array('error' => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;



?>