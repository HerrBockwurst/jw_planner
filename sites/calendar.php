<?php
if(!isset($fromIndex)): header("Location:".getURL()); exit; endif;

while(true):

	/*
	 * Modul Kalender
	 */

	require_once 'sites/modules/user/calendar.php';
	break;
endwhile;

?>