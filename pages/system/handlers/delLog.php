<?php
if(!isset($_POST['rel']) || $_POST['rel'] == '') exit;
global $mysql;

$mysql->where("release", $_POST["rel"]);
if(!$mysql->delete("changelog")) returnErrorJSON(getString("errors sql"));
echo json_encode(array());
