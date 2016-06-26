<?php
while(true):
	/*
	 * Testet ob Benutzer die Berechtigung für Versammlung hat
	 */
	
	if($_POST['versammlung'] != $USER->versammlung &&
		!$USER->hasPerm('admin.useredit.vs.'.$_POST['versammlung']) &&
		!$USER->hasPerm('admin.useredit.global')):
		$ERROR['useradd'] = getLang('errors>noperm');
		break;
	endif;
	
	/*
	 * Teste Permissions
	 */
	$perms = array();
	foreach($_POST AS $key => $value):
		if(strpos($key, 'permission') !== false) $perms[] = str_replace('_', '.', substr($key, 11));
			if(strpos($key, 'permission') !== false && !$USER->hasPerm(str_replace('_', '.', substr($key, 11)))):
				$ERROR['useradd'] = getLang('errors>noperm');
				break 2;
		endif;
	endforeach;
	
break;		
endwhile;
?>
