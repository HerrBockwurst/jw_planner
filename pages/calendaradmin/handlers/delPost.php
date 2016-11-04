<?php
if(!isset($_POST)) returnErrorJSON('No Data given!');

global $mysql, $user;

$mysql->where('pattern.patt_id', $_POST['patt_id']);
$mysql->join(array("pattern" => 'cid', "calendar" => "cid"));
$mysql->join(array("calendar" => 'vsid', "versammlungen" => "vsid"));
$mysql->select("pattern", array('versammlungen.vsid' => "vsid"), 1);

if($mysql->countResult() != 1) exit;
if($mysql->fetchRow()->vsid != $user->vsid) exit;

/*
 * Pattern löschen
 */
$mysql->where('patt_id', $_POST['patt_id']);
if(!$mysql->delete('pattern')) returnErrorJSON(getString('errors sql'));