<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('admin.calendar')) exit;

/*
 * Teste Versammlungsrechts
 */

$cvs = $mysql->execute("SELECT vsid FROM calendar WHERE cid = ? LIMIT 1", 'i', $_POST['cid']);
if($cvs->num_rows != 1):
	$data = array("error" => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$cvs = $cvs->fetch_assoc();

// Versammlung zu der der Kalender geändert werden soll
if(!array_key_exists($_POST['vs'], getVSArray())):
	$data = array("error" => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;

//Versammlung aus der der Kalender stammt
if(!array_key_exists($cvs['vsid'], getVSArray())):
	$data = array("error" => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;

/*
 * Teste auf löschen
 */

if($_POST['del'] == 1):
	/*
	 * PID's abrufen
	 */

	$pids = $mysql->execute("SELECT pid FROM posts WHERE cid = ?", 'i', $_POST['cid']);
	$pids = $pids->fetch_all(MYSQLI_ASSOC);

	if(!$mysql->execute("DELETE FROM calendar WHERE cid = ?", 'i', $_POST['cid'])):
		$data = array("error" => getString('errors>MySQL'));
		echo json_encode($data);
		exit;
	endif;
	
	if(!$mysql->execute("DELETE FROM posts WHERE cid = ?", 'i', $_POST['cid'])):
		$data = array("error" => getString('errors>MySQL'));
		echo json_encode($data);
		exit;
	endif;
	
	/*
	 * Entrys anhand der PID's löschen
	 */
	
	foreach($pids AS $pid):
		if(!$mysql->execute("DELETE FROM entrys WHERE pid = ?", 'i', $pid['pid'])):
			$data = array("error" => getString('errors>MySQL'));
			echo json_encode($data);
			exit;
		endif;
	endforeach;
	
	$data = array("success" => getString('admin>c_deleted'), "deleted" => true);
	echo json_encode($data);
	exit;
endif;

/*
 * Teste auf leeren Wert
 */

if($_POST['name'] == ''):
	$data = array("error" => getString('errors>invalidFormSubmit'));
	echo json_encode($data);
	exit;
endif;

/*
 * MySQL eintragen
 */
	
if(!$mysql->execute("UPDATE calendar SET name = ?, vsid = ? WHERE cid = ?", 'ssi', array($_POST['name'], $_POST['vs'], $_POST['cid']))):
	$data = array("error" => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$data = array("success" => getString('admin>c_edited'));
echo json_encode($data);
exit;

?>