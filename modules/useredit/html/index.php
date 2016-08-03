<?php
if(!defined('index')) exit;

switch(getURL(4)):
	case 'search':
		require_once 'modules/useredit/html/search.php';
		break;
	default:
		require_once 'modules/useredit/html/mainmodul.php';
		break;
endswitch;

