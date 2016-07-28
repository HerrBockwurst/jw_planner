<?php
if(!defined('index')) exit;


$data = array('login', 'index.php');
registerModul($data);
addDataHandler(array(MODUL, 'login', 'posthandler.php'));