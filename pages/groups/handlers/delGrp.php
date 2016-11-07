<?php
if(!isset($_POST)) exit;

global $mysql, $user;

$mysql->where('gid', $_POST['gid']);
$mysql->where('vsid', $user->vsid);
if(!$mysql->delete('groups')) returnErrorJSON(getString('errors sql'));