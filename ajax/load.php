<?php
if(!defined('index')) exit;

switch(getURL(2)):
	case 'modul':
		$bob->build(array(DIRECT, 'modules/'.getURL(3).'/html/index.php'));
		break;
	default:
		$bob->build(array(PAGE, 'main'));
		$bob->build(array(PAGE, 'topbar'));
		$bob->build(array(PAGE, 'menu'));
		break;
endswitch;