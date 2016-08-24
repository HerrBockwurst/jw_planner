<?php
if(!defined('index')) exit;

$data = array('calendaradmin', 'index.php', MENUVIS, 'admin.calendar');
registerModul($data);
addDataHandler(array(MODUL, 'addcal', 'calendaradmin/handlers/addcal.php'));
addDataHandler(array(MODUL, 'editcal', 'calendaradmin/handlers/editcal.php'));
addDataHandler(array(MODUL, 'addpost', 'calendaradmin/handlers/addpost.php'));
addDataHandler(array(MODUL, 'getposts', 'calendaradmin/handlers/getposts.php'));
/*
addDataHandler(array(MODUL, 'adduser', 'useredit/handlers/adduser.php'));
addDataHandler(array(MODUL, 'edituser', 'useredit/handlers/edituser.php'));
addDataHandler(array(MODUL, 'updateuser', 'useredit/handlers/updateuser.php'));
 */