<?php
if(!isset($_POST)) exit;

global $user, $mysql;

if(!$mysql->insert('groups', array('name' => $_POST['name'], 'vsid' => $user->vsid, 'members' => "[]")))
	returnErrorJSON(getString('errors sql'));
