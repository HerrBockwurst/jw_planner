<?php
if(!defined('index')) exit;

switch(getURL(2)):
	case 'modul':
		if(!getURL(4)) $file = "index";
		else $file = getURL(4);
		$bob->build(array(DIRECT, 'modules/'.getURL(3).'/html/'.$file.'.php'));
		break;
	case 'page':
		
		break;
	default:
		$bob->build(array(PAGE, 'main'));
		$bob->build(array(PAGE, 'topbar'));
		$bob->build(array(PAGE, 'menu'));
		break;
endswitch;