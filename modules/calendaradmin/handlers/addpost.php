<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.admin')) exit;

/*
 * Teste Berechtigung für Kalender
 */

$cal = $mysql->execute("SELECT vsid FROM calendar WHERE cid = ?", 'i', intval($_POST['cid']));

if($cal->num_rows != 1):
	$data = array("error" => getString('errors>noCal'));
	echo json_encode($data);
	exit;
endif;

$cal = $cal->fetch_assoc();

if(!array_key_exists($cal['vsid'], getVSArray())):
	$data = array("error" => getString('errors>noPerm'));
	echo json_encode($data);
	exit;
endif;

/*
 * Test auf Valide Daten
 */

$pattern = "/[0-9]{1,2}.[0-9]{1,2}.[0-9]{4} [0-9]{1,2}:[0-9]{2}/";

if(!preg_match($pattern, $_POST['start']) || !preg_match($pattern, $_POST['end']) || !preg_match($pattern, $_POST['expire'])):
	$data = array("error" => getString('errors>invalidFormSubmit'));
	echo json_encode($data);
	exit;	
endif;

/*
 * Konvertiere Daten zu Timestamp
 */

$start = strtotime($_POST['start']);
$end = strtotime($_POST['end']);
$expire = strtotime($_POST['expire']);

if(!$start || !$end || !$expire):
	$data = array("error" => getString('errors>invalidFormSubmit'));
	echo json_encode($data);
	exit;
endif;

/*
 * Prüfe auf Valide Reihenfolge (start < end < expire)
 */

if($start > $end):
	$data = array("error" => getString('errors>PostTimeFailure'));
	echo json_encode($data);
	exit;
endif;

if($end > $expire):
	$data = array("error" => getString('errors>PostTimeFailure'));
	echo json_encode($data);
	exit;
endif;

/*
 * Personenanzahl testen
 */

$pers = intval($_POST['pers']);

if($pers <= 0 || $pers > MAXPERS):
	$data = array("error" => preg_replace("/\?#\?/", MAXPERS, getString('errors>invalidPers')));
	echo json_encode($data);
	exit;
endif;

/*
 * Alles OK, POST eintragen
 */

if(!$mysql->execute("INSERT INTO posts (cid, start, end, expire, count) VALUES (?,?,?,?,?)", 'iiiii', array(intval($_POST['cid']), $start, $end, $expire, $pers))):
	$data = array("error" => getString('errors>MySQL'));
	echo json_encode($data);
	exit;
endif;

$data = array("success" => getString('admin>post_added'));
echo json_encode($data);
exit;
?>