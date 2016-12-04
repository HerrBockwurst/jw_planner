<?php
if(!isset($_POST['msg']) || !isset($_POST['title'])) exit;

global $mysql;
$text = preg_replace('/<script/', '&lt;script', $_POST['msg']);
$title = preg_replace('/<script/', '&lt;script', $_POST['title']);

if(strlen($text) == 0) {
	$mysql->where('sender', 'system');
	if(!$mysql->delete('messages')) returnErrorJSON(getString('errors sql'));
	echo json_encode(array());
	exit;
} else {
	$mysql->where('sender', 'system');
	$mysql->select('messages');
	if($mysql->countResult() == 0) {
		if(!$mysql->insert('messages',
			array('sender' => 'system', 'created' => time(), 'title' => $title, 'content' => $text, 'expire' => PHP_INT_MAX, 'recipient' => 'all'))) returnErrorJSON(getString('errors sql'));
		echo json_encode(array());
		exit;
	} else {
		$mysql->where('sender', 'system');
		if(!$mysql->update('messages',
				array('created' => time(), 'title' => $title, 'content' => $text))) returnErrorJSON(getString('errors sql'));
		echo json_encode(array());
		exit;
	}
}