<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.admin')) exit;

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
	if(!$mysql->execute("DELETE FROM calendar WHERE cid = ?", 'i', $_POST['cid'])):
		$data = array("error" => getString('errors>MySQL'));
		echo json_encode($data);
		exit;
	endif;
	
	//TODO Posts
	
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