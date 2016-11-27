<?php
if(!isset($_POST['msg'])) exit;

if(!$mysql->insert('messages', array('content' => $_POST['msg'],
		'sender' => $user->uid, 
		'recipient' => 'all', 
		'created' => time(), 
		'expire' => PHP_INT_MAX)))
	returnErrorJSON(getString('errors sql'));
?>
