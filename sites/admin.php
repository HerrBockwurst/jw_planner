<?php

while(true):
	
	if(!$USER->hasPerm('admin.visible')) header("Location:".printURL());
	
	if($url->value(1) == 'newuser'):
		require_once 'sites/modules/admin/newuser.php';
		break;
	elseif($url->value(1) == 'searchuser'):
		require_once 'sites/modules/admin/searchuser.php';
		break;
	elseif($url->value(1) == 'useredit'):
		require_once 'sites/modules/admin/useredit.php';
		break;
	endif;
	
	/*
	 * Modul Benutzer 
	 */
	
	if($USER->hasPerm('admin.useredit')) require_once 'sites/modules/admin/ucp.php';

	/*
	 * Modul Kalender
	 */
	
	if($USER->hasPerm('admin.calendar')) require_once 'sites/modules/admin/ccp.php';
	
	break;
endwhile;
?>