<?php
if(!defined('index')) exit;

$data = array('useredit', 'index.php', MENUVIS, 'admin.useredit');
registerModul($data);
addDataHandler(array(MODUL, 'searchuser', 'searchuser.php'));
addDataHandler(array(MODUL, 'adduser', 'adduser.php'));
addDataHandler(array(MODUL, 'edituser', 'edituser.php'));