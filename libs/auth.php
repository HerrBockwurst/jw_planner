<?php
/*
 * Weiterleiten, wenn keine aktuelle Session besteht
 */

//if(!isset($_SESSION['dbid']) && $url->value(0) != 'login' ) header("Location:".getURL()."/login");

$result = $mysql->query('SELECT * FROM `sessions`', true);
while($row = $result->fetch_assoc()):
	
	if($row['expire'] < time()):
		/*
		 * L�sche alte Sessions
		 */
		if($mysql->query('DELETE FROM `sessions` WHERE `user` = \''.$row['user'].'\''))
			$log->write('Session f�r Benutzer \''.$row['user'].'\' erfolgreich gel�scht!');			
		else
			$log->write('error', 'Fehler beim L�schen der Session f�r Benutzer \''.$row['user'].'\'! ['.$mysql->error().']');
		$mysql->free();
		return;		
	endif;
	
	if($row['sid'] == $_SESSION['dbid']):
		
	endif;
		
endwhile;
$mysql->free();
?>