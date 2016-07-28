<?php
if(!defined('index')) exit;

switch(getURL(1)):
	case 'load':
		require_once 'ajax/load.php';
		break;
	case 'datahandler':
		getDataHandler(getURL(2));
		break;
	default:
		break;
endswitch;