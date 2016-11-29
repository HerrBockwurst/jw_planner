<?php
if(!isset($_POST['id']) || !isset($_POST['lang_de']) || $_POST['id'] == '' || $_POST['lang_de'] == '') exit;

$text = array('de' => preg_replace('/<script/', '&lt;script', $_POST['lang_de']));
global $mysql;

//Teste ob Log existiert
$mysql->where('release', $_POST['id']);
$mysql->select('changelog');
if($mysql->countResult() != 0) {
	$mysql->where('release', $_POST['id']);
	if(!$mysql->update('changelog', array('changelog' => json_encode($text)))) returnErrorJSON(getString('errors sql'));
	echo json_encode(array());
	exit;
}
	
if(!$mysql->insert('changelog', array('release' => $_POST['id'], 'changelog' => json_encode($text)))) returnErrorJSON(getString('errors sql'));
echo json_encode(array());
exit;