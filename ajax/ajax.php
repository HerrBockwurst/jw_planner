<?php
	echo hi;
	switch($url->value(1)):
		case 'updateCal':
			require_once 'ajax/updateCal.php';
			break;
		default:
			break;
	endswitch;
	
	exit;
?>