<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.admin')) exit;

/*
 * Teste auf leeren Wert
 */

if($_POST['name'] == ''):
	$data = array("error" => getString('errors>invalidFormSubmit'));
	echo json_encode($data);
	exit;
endif;

/*
 * Teste Versammlungsrechts
 */

if(!array_key_exists($_POST['vs'], getVSArray())):
	$data = array("error" => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;

/*
 * MySQL eintragen
 */
	
if(!$mysql->execute("INSERT INTO calendar (name, vsid, admin) VALUES (?,?,?)", 'sss', array($_POST['name'], $_POST['vs'], $user->uid))):
	$data = array("error" => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$data = array("success" => getString('admin>c_added'));
echo json_encode($data);
exit;

?>