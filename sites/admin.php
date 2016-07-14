<?php

while(true):
	
	if(!$USER->hasPerm('admin.visible')): header("Location:".getURL()); exit; endif;

	switch($url->value(1)):
		case 'newuser':
			require_once 'sites/modules/admin/newuser.php';
			break 2;
		case 'searchuser':
			require_once 'sites/modules/admin/searchuser.php';
			break 2;
		case 'useredit':
			require_once 'sites/modules/admin/useredit.php';
			break 2;
		case 'newcal':
			require_once 'sites/modules/admin/newcal.php';
			break 2;
		case 'editcal':
			require_once 'sites/modules/admin/editcal.php';
			break 2;
		case 'deletepost':
			require_once 'libs/deletepost.php';
			break;
	
	endswitch;
	
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