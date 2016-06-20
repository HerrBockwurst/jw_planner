<?php while(true):

if(!$USER->hasPerm('admin.visible')) header("Location:".printURL());

if($url->value(1) == 'newuser'):
	require_once 'sites/modules/admin/newuser.php';
	break;
endif;

/*
 * Modul Benutzer 
 */

if($USER->hasPerm('admin.useredit')) require_once 'sites/modules/admin/ucp.php';

	break;
endwhile;
?>