<?php
if(!isset($_POST)) returnErrorJSON('No Data given!');

global $mysql, $user;

$mysql->where('cid', $_POST['cid']);
$mysql->join(array("calendar" => 'vsid', "versammlungen" => "vsid"));
$mysql->select("calendar", array('versammlungen.vsid' => "vsid"), 1);

if($mysql->countResult() != 1) exit;
if($mysql->fetchRow()->vsid != $user->vsid) exit;

/*
 * Pattern löschen
 */

$mysql->where('cid', $_POST['cid']);
if(!$mysql->delete('pattern')) returnErrorJSON(getString('errors sql'));
$mysql->where('cid', $_POST['cid']);
if(!$mysql->delete('posts')) returnErrorJSON(getString('errors sql'));
$mysql->where('cid', $_POST['cid']);
if(!$mysql->delete('calendar')) returnErrorJSON(getString('errors sql'));