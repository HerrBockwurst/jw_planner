<?php
if(!defined('index')) exit;
global $mysql;

while(true):
	if(!isset($_POST['username']) || !isset($_POST['password']) || $_POST['password'] == "" || $_POST['username'] == ""):
		$data = array('error' => getString('errors>invalidFormSubmit'));
		echo json_encode($data);
		break;
	endif;
	
	$result = $mysql->execute("SELECT uid, password FROM user WHERE uid = ? OR email = ? LIMIT 1", 'ss', array($_POST['username'], $_POST['username']));
	
	/*
	 * Testen ob Datenbank funtioniert
	 */
	if(!$result):
		$data = array('error' => getString('errors>MySQL'));
		echo json_encode($data);
		break;
	endif;
	
	/*
	 * Testen ob Benutzer gefunden wurde
	 */
	if($result->num_rows != 1):
		$data = array('error' => getString('errors>wrongAuth'));
		echo json_encode($data);
		break;
	endif;
	
	/*
	 * Passwort checken
	 */
	
	$result = $result->fetch_assoc();
	
	if($result['password'] != hash('sha512', $_POST['password'])):
		$data = array('error' => getString('errors>wrongAuth'));
		echo json_encode($data);
		break;
	endif;
	
	$uid = $result['uid'];
	
	/*
	 * Session eintragen + Variable setzen
	 */
	
	if(!$mysql->execute("INSERT INTO `sessions`(`sid`, `uid`, `expire`) VALUES (?,?,?)", 'sss',
						array(session_id(), $uid, getSQLDate(time() + (SESSIONTIME * 60))))):
		$data = array('error' => getString('errors>MySQL'));
		echo json_encode($data);
		break;
	endif;
	
	$_SESSION['sid'] = session_id();
	
	$data = array('success' => true);
	echo json_encode($data);
	break;
endwhile;
?>