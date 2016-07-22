<?php
if(!isset($index)) exit;

switch(getURL(1)):
	case 'load':
		require_once 'ajax/load.php';
		break;
	default:
		break;
endswitch;