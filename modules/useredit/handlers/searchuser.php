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

$vs_subperms = $user->getSubPerm('.vs.');
if(!$vs_subperms):
	$versammlungen = array($user->vsid); // <- Liste der erlaubten Versammlungen ODER Liste mit Permissions
else:
	$versammlungen = array();
	foreach($vs_subperms AS $cvperms):
		$cv = explode('.', $cvperms);
		if($cv[count($cv) - 1] == '*') $versammlungen[] = '*'; // Durchsuchen ob Wildcard vorliegt
		else $versammlungen[] = $cv[count($cv) - 1]; //<- Array mit Versammlungen (vsid)
	endforeach;
	
endif;

/*
 * Abfrage
 */

$result = $mysql->execute("SELECT u.uid, u.name, u.vsid, v.name AS vname
							FROM user AS u
							INNER JOIN versammlungen AS v ON (u.vsid = v.vsid)
							WHERE u.uid LIKE ? AND u.vsid LIKE ?",
							'ss', array('%'.$_POST['username'].'%', '%'.$_POST['versammlung'].'%'));

if($result->num_rows == 0):
	$data = array('error' => getString('errors>usersearch_noUserFound'));
	echo json_encode($data);
	exit;
endif;

$result = $result->fetch_all(MYSQLI_ASSOC);

/*
 * Wenn keine Wildcard vorhanden, dann result filtern und nur erlaubte Versammlungen drin lassen
 */

if(!in_array('*', $versammlungen)):
	while($row = current($result)):
		if(!in_array($row['vsid'], $versammlungen)) unset($result[key($result)]);
		else next($result);
	endwhile;
endif;


/*
 * Array muss noch UTF8 Encodiert werde, weil json_encode nur utf8 strings bearbeiten kann
 */
reset($result);
while($row = current($result)):
	$result[key($result)] = array_map("utf8_encode", $row);
	next($result);
endwhile;

if(empty($result)):
	$data = array('error' => getString('errors>usersearch_noUserFound'));
	echo json_encode($data);
	exit;
endif;

/*
 * Array neu sortieren, weil es sonst zu anzeigefehlern kommen kann
 */
$result = array_values($result);

echo json_encode($result);

?>
