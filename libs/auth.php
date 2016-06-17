<?php
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

if(!isset($_SESSION['dbid']) && $url->value(0) != 'login' ) header("Location:".getURL()."/login");

/*
 * Pr�fung ob g�ltige Session vorhanden
 */

if(isset($_SESSION['dbid'])):
	$result = $mysql->execute("SELECT * FROM `sessions` WHERE `sid` = ? LIMIT 1", "s", $_SESSION['dbid']);
	if($result->num_rows != 1):
		unset($_SESSION['dbid']);
		header("Location:".getURL()."/login");
	else:
		
		if(!$mysql->execute("UPDATE `sessions` SET `expire` = '".date("Y-m-d H:i:s",time()+($CONFIG['sessiontime']*60))."' WHERE `sid` = ?", 's', $_SESSION['dbid']))
			echo "Fehler";
	endif;
	
	
endif;
?>