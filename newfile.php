<?php
if($_POST['versammlung'] != $USER->versammlung &&
		!$USER->hasPerm('admin.useredit.vs.'.$_POST['versammlung']) &&
		!$USER->hasPerm('admin.useredit.global')):
		$ERROR['useradd'] = getLang('errors>noperm');
		break;
		endif;


		/*
		 * Teste Permissions
		 */

		foreach($_POST AS $key => $value):
		if(strpos($key, 'permission') !== false && !$USER->hasPerm(str_replace('_', '.', substr($key, 11)))):
		$ERROR['useradd'] = getLang('errors>noperm');
		break 2;
		endif;
		endforeach;

		/*
		 * Teste auf alles ausgefüllt
		 */

		if($_POST['name'] == "" || $_POST['password'] == "" || $_POST['password2'] == "" || $_POST['versammlung'] == ""):
		$ERROR['useradd'] = getLang('errors>emptyfields');
		break;
		endif;

		/*
		 * Teste ob Passwörter überein stimmen
		 */

		if(utf8_decode($_POST['password']) != utf8_decode($_POST['password2'])):
		$ERROR['useradd'] = getLang('errors>passwordnomatch');
		break;
		endif;