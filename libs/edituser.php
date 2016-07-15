<?php
if(!isset($fromIndex)) exit;

while(true):
	/*
	 * TODO
	 * WICHTIG: Schutz einbauen, damit man nicht im Formular das Target abändern kann -> Prüfen ob das Target auch wirklich mit
	 * den übergebenen Daten übereinstimmt -> Versammlungscheck
	 */

	/*
	 * Testet ob Benutzer die Berechtigung für Versammlung hat
	 */	
	if($_POST['versammlung'] != $USER->versammlung &&
		!$USER->hasPerm('admin.useredit.vs.'.$_POST['versammlung']) &&
		!$USER->hasPerm('admin.useredit.global')):
		$ERROR['useredit'] = getLang('errors>noperm');
		break;
	endif;
	
	/*
	 * Benutzer löschen & Sessions löschen
	 */
	
	if(isset($_POST['delete'])):
		//Lösche User
		$result = $mysql->execute("DELETE FROM `users` WHERE `uid` = ?", 's', $url->value(2));
		if($result != true): //Evtl Error loggen
			$ERROR['useredit'] = getLang('errors>mysql');
			$log->write("Ein Fehler beim Löschen des Benutzers '".$url->value(2)."' ist aufgetreten: ".$mysql->error(), 'error');
		endif;
		
		//Lösche Sessions
		$result = $mysql->execute("DELETE FROM `sessions` WHERE `user` = ?", 's', $url->value(2));
		if($result != true): //Evtl Error loggen
			$ERROR['useredit'] = getLang('errors>mysql');
			$log->write("Ein Fehler beim Löschen der Session (durch Update) des Benutzers '".$url->value(2)."' ist aufgetreten: ".$mysql->error(), 'error');
		endif;
		
		//Lösche Permissions
		$result = $mysql->execute("DELETE FROM `permissions` WHERE `uid` = ?", 's', $url->value(2));
		if($result != true): //Evtl Error loggen
			$ERROR['useredit'] = getLang('errors>mysql');
			$log->write("Ein Fehler beim Löschen der Permissions (durch Update) des Benutzers '".$url->value(2)."' ist aufgetreten: ".$mysql->error(), 'error');
		endif;
		
		$SUCCESS['userdel'] = true;
		/*
		 * TODO Kalendereinträge löschen
		 * TODO Kalender umwidmen
		 */
		$log->write("[".$USER->username."] Benutzer '".$url->value(2)."' erfolgreich gelöscht!");
		break;
		
	endif;
	
	/*
	 * Teste Permissions
	 */
	
	$perms = array();
	$permsToDelete = array();
	foreach($_POST AS $key => $value):
		if(strpos($key, 'permission') !== false) $perms[] = str_replace('_', '.', substr($key, 11)); //Array mit Permissions erstellen
		if(strpos($key, 'permission') !== false && !$USER->hasPerm(str_replace('_', '.', substr($key, 11)))): //Wenn Benutzer Formular manipuliert und Andere Permissions einträgt als er hat
			$ERROR['useredit'] = getLang('errors>noperm');
			break 2;
		endif;
		
		//Array mit Allen Permissions in der Auswahl erstellen und anschließen übergebene Permissions löschen
		if(strpos($key, 'hidden') !== false) $permsToDelete[] = str_replace('_', '.', substr($key, 7)); //Array mit Permissions erstellen
		if(strpos($key, 'hidden') !== false && !$USER->hasPerm(str_replace('_', '.', substr($key, 7)))): //Wenn Benutzer Formular manipuliert und Andere Permissions einträgt als er hat
			$ERROR['useredit'] = getLang('errors>noperm');
			break 2;
		endif;		
	endforeach;
		
	//Permissions die weiterhin angehäckelt sind, aus PermsToDelete löschen
	foreach($perms AS $perm) if(isset($permsToDelete[array_search($perm, $permsToDelete)])) unset($permsToDelete[array_search($perm, $permsToDelete)]);	
	
	$udata = array(); //Init Array mit updatedaten
	
	/*
	 * Teste ob Passwort aktualisiert werden muss
	 */
	
	if($_POST['password'] != ""):
		if($_POST['password'] != $_POST['password2']): //Passwörter stimmen nicht überein
			$ERROR['useredit'] = getLang('errors>passwordnomatch');
			break;
		endif;
		
		$udata['password'] = hash('sha512', utf8_decode($_POST['password']));
	endif;
	
	/*
	 * Update-Daten erstellen
	 */
	
	$udata['versammlung'] = $_POST['versammlung'];
	$udata['name'] = $_POST['name'];
	$udata['email'] = $_POST['email'];
	
	/*
	 * Passwort Auslaufdatum setzen NUR wenn neues Passwort gesetzt wurde
	 */
	if(isset($udata['password']) && isset($_POST['noexpire'])) $udata['p_eval'] = getSQLDate(PHP_INT_MAX);  
	elseif(isset($udata['password']) && !isset($_POST['noexpire'])) $udata['p_eval'] = getSQLDate();
	
	/*
	 * Active setzten oder löschen
	 */
	
	if(isset($_POST['active'])) $udata['status'] = 'active';
	else $udata['status'] = 'inactive';	
	
	$keys = array();
	$data = array();
	
	foreach($udata AS $key => $value):
		//Damit daten richtig geordnet werden
		$keys[] = $key;
		$data[] = $value; 
	endforeach;
	
	$data[] = $url->value(2);
	
	/*
	 * String aus Keys erstellen
	 */
	
	$keystring = "";
	foreach($keys AS $value) $keystring .= "`".$value."` = ?,";
	$keystring = substr($keystring, 0, -1); //Letztes Komma löschen
	
	$patternstring = "";
	for($i=1; $i <= (count($keys) + 1); $i++) $patternstring .= "s"; //sss String erstellen mit einem s mehr als Keys, da uid nach where übergeben werden muss
	
	/*
	 * Benutzer Updaten
	 */
	
	$result = $mysql->execute("UPDATE `users` SET ".$keystring." WHERE `uid` = ?", $patternstring, $data);	
	
	if($result != true): //Evtl Error loggen
		$ERROR['useredit'] = getLang('errors>mysql');
		$log->write("Ein Fehler beim Updates des Benutzers '".$url->value(2)."' ist aufgetreten: ".$mysql->error(), 'error');
	endif;
	
	/*
	 * Permissions updaten
	 */
	
	
	foreach($perms AS $perm):		
		$result = $mysql->execute("SELECT * FROM `permissions` WHERE `uid` = ? AND `perm` = ?", 'ss', array($url->value(2), $perm));
		if($result === false):		
			$ERROR['useredit'] = getLang('errors>mysql');
			$log->write("Fehler beim Abrufen der Permission '".$perm."' für User '".$url->value(2)."': ".$mysql->error(), 'error');
			break 2;
		endif;
		if($result->num_rows == 0):
			$result = $mysql->execute("INSERT INTO `permissions` (`uid`, `perm`) VALUES (?,?)", 'ss', array($url->value(2), $perm));
			if($result === false):
				$ERROR['useredit'] = getLang('errors>mysql');
				$log->write("Fehler beim Einfügen der Permission '".$perm."' für User '".$url->value(2)."': ".$mysql->error(), 'error');
				break 2;
			endif;
		endif;
	endforeach;
	
	foreach($permsToDelete AS $perm):
		$result = $mysql->execute("DELETE FROM `permissions` WHERE `uid` = ? AND `perm` = ?", 'ss', array($url->value(2), $perm));
		if($result === false):
			$ERROR['useredit'] = getLang('errors>mysql');
			$log->write("Fehler beim Löschen der Permission '".$perm."' für User '".$url->value(2)."': ".$mysql->error(), 'error');
			break 2;
		endif;
	endforeach;
	
	/*
	 * Benutzer ausloggen
	 */
	
	$result = $mysql->execute("DELETE FROM `sessions` WHERE `user` = ?", 's', $url->value(2));
	if($result != true): //Evtl Error loggen
		$ERROR['useredit'] = getLang('errors>mysql');
		$log->write("Ein Fehler beim Löschen der Session (durch Update) des Benutzers '".$url->value(2)."' ist aufgetreten: ".$mysql->error(), 'error');
	endif;
	
	$SUCCESS['useredit'] = getLang('admin>user_edited');
	
break;		
endwhile;
?>
