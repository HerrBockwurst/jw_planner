<?php
if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;

$redirect = false; //Definition der Redirectvariable f�r Ajax

function doRedirect() {
	global $url, $redirect;
	
	/*
	 * Weiterleiten wenn Seite als Ajax aufgerufen wurde
	 */
	
	if($url->value(0) == 'ajax'):
		$redirect = true;
	else:
		
		/*
		 * Weiterleitung
		 */
		header("Location:".getURL()."/login");
		exit;
	endif;
}



/*
 * Weiterleiten, wenn keine aktuelle Session besteht
 */

$result = $mysql->execute('SELECT * FROM `sessions`');

while($row = $result->fetch_assoc()):
	
	if(strtotime($row['expire']) < time()):
		/*
		 * L�sche alte Sessions
		 */
		if($mysql->execute('DELETE FROM `sessions` WHERE `user` = \''.$row['user'].'\'') != false)
			$log->write('Session f�r Benutzer \''.$row['user'].'\' erfolgreich gel�scht!');
		else
			$log->write('error', 'Fehler beim L�schen der Session f�r Benutzer \''.$row['user'].'\'! ['.$mysql->error().']');
		
	endif;
	
	if(isset($_SESSION['dbid']) && $row['sid'] == $_SESSION['dbid']):
		$_SESSION['dbid'] = $row['sid'];
	endif;	
		
endwhile;

if(!isset($_SESSION['dbid']) && $url->value(0) != 'login' ) doRedirect();

/*
 * Pr�fung ob g�ltige Session vorhanden
 */

if(isset($_SESSION['dbid'])):
	$result = $mysql->execute("SELECT * FROM `sessions` WHERE `sid` = ? LIMIT 1", "s", $_SESSION['dbid']);
	if($result->num_rows != 1):
		/*
		 * Zum Login falls Session-Variable vorhanden, aber keine Session in DB besteht
		 */
		unset($_SESSION['dbid']);
		doRedirect();
	else:
		/*
		 * Update Session
		 */
		if(!$mysql->execute("UPDATE `sessions` SET `expire` = '".getSQLDate(time()+($CONFIG['sessiontime']*60))."' WHERE `sid` = ?", 's', $_SESSION['dbid']))
			$log->write("Konnte Session f�r User", "error"); //TODO
		/*
		 * Erstelle User-Objekt
		 */
		$USER = new user();
		
	endif;
	
	
endif;
?>