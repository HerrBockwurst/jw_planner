<?php
/*
 * Weiterleiten, wenn keine aktuelle Session besteht
 */

//if(!isset($_SESSION['dbid']) && $url->value(0) != 'login' ) header("Location:".getURL()."/login");

$result = $mysql->query('SELECT * FROM `sessions`', true);
while($row = $result->fetch_assoc()):
	
	if($row['expire'] < time()):
		/*
		 * Lösche alte Sessions
		 */
		if($mysql->query('DELETE FROM `sessions` WHERE `user` = \''.$row['user'].'\''))
			$log->write('Session für Benutzer \''.$row['user'].'\' erfolgreich gelöscht!');			
		else
			$log->write('error', 'Fehler beim Löschen der Session für Benutzer \''.$row['user'].'\'! ['.$mysql->error().']');
		$mysql->free();
		return;		
	endif;
	
	if($row['sid'] == $_SESSION['dbid']):
		
	endif;
		
endwhile;
$mysql->free();
?>