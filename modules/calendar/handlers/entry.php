<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.entry')) exit;

if(!isset($_POST['pid']) || !isset($_POST['cid'])) exit;

$result = $mysql->execute("SELECT e.*, c.vsid, p.count FROM entrys AS e
							INNER JOIN calendar AS c ON (e.cid = c.cid)
							INNER JOIN posts AS p ON (e.pid = p.pid)
							WHERE e.pid = ?", 'i', $_POST['pid']);
$entrys = $result->fetch_all(MYSQLI_ASSOC);

$full = false; 
foreach($entrys AS $entry):
	/*
	 * Pr�fe ob Nutzer Rechte f�r vsid hat
	 */

	if(!array_key_exists($entry['vsid'], getVSArray())):
		echo json_encode(array("error" => getString('errors>noPerm')));
		exit;
	endif;
	
	/*
	 * Pr�fen ob nutzer schon eingetragen ist
	 */
	
	if($entry['uid'] == $user->uid):
		$remove = $entry['eid'];
		$full = false;
		break;
	endif;
	
	/*
	 * Pr�ft ob Kalender voll ist
	 */
	
	if(count($entrys) >= intval($entry['count']) && !isset($remove)) $full = true;
		
	
	
endforeach;

if($full):
	echo json_encode(array("error" => getString('errors>postFull')));
	exit;
endif;



if(!isset($remove)):
	/*
	 * Eintrag erzeugen
	 */
	if(!$mysql->execute("INSERT INTO entrys (pid, cid, uid) VALUES (?,?,?)", 'iis', array($_POST['pid'], $_POST['cid'], $user->uid))):
		echo json_encode(array("error" => getString('errors>MySQL')));
		exit;
	endif;
	
	echo json_encode(array("success" => getString('calendar>entry_successful'), "newcount" => count($entrys) + 1));
	exit;
endif;

/*
 * Datensatz l�schen
 */
if(!$mysql->execute("DELETE FROM entrys WHERE eid = ?", 'i', $remove)):
	echo json_encode(array("error" => getString('errors>MySQL')));
	exit;
endif;

echo json_encode(array("success" => getString('calendar>entry_successful_remove'), "newcount" => count($entrys) - 1));