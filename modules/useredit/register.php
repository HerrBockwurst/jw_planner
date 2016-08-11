<?php
if(!defined('index')) exit;

$data = array('useredit', 'index.php', MENUVIS, 'admin.useredit');
registerModul($data);
addDataHandler(array(MODUL, 'searchuser', 'useredit/handlers/searchuser.php'));
addDataHandler(array(MODUL, 'adduser', 'useredit/handlers/adduser.php'));
addDataHandler(array(MODUL, 'edituser', 'useredit/handlers/edituser.php'));
addDataHandler(array(MODUL, 'updateuser', 'useredit/handlers/updateuser.php'));