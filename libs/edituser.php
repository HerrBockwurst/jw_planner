<?php
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
	 * Teste Permissions
	 */
	$perms = array();
	foreach($_POST AS $key => $value):
		if(strpos($key, 'permission') !== false) $perms[] = str_replace('_', '.', substr($key, 11)); //Array mit Permissions erstellen
		if(strpos($key, 'permission') !== false && !$USER->hasPerm(str_replace('_', '.', substr($key, 11)))): //Wenn Benutzer Formular manipuliert und Andere Permissions einträgt als er hat
			$ERROR['useredit'] = getLang('errors>noperm');
			break 2;
		endif;
	endforeach;
	
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
	
	/*
	 * 2 Query, je nachdem ob Passwort übergeben wurde oder es bleiben soll
	 */
	
	$udata['uid'] = $url->value(2);
	
	if(isset($udata['password']))	
		$result = $mysql->execute("UPDATE `users` SET `password` = ?, `versammlung` = ?, `name` = ?, `email` = ?, `status` = ? WHERE `uid` = ?", 'sssss');
	else
		$result = $mysql->execute("UPDATE `users` SET `versammlung` = ?, `name` = ?, `email` = ?, `status` = ? WHERE `uid` = ?");
	
	var_dump($_POST);
	
break;		
endwhile;
?>
