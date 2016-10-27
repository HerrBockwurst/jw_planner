<?php
if(!isset($_POST['name'])) returnErrorJSON("No name given!");
if($_POST['name'] == '') returnErrorJSON(getString('errors FormfillError'));

global $mysql, $user;
if(!$mysql->insert('calendar', array('name' => $_POST['name'], 'vsid' => $user->vsid))) returnErrorJSON(getString('errors sql'));