<?php
if(!defined('index')) exit;

$data = array('calendaradmin', 'index.php', MENUVIS, 'admin.calendar');
registerModul($data);
addDataHandler(array(MODUL, 'addcal', 'calendaradmin/handlers/addcal.php'));
addDataHandler(array(MODUL, 'editcal', 'calendaradmin/handlers/editcal.php'));
addDataHandler(array(MODUL, 'addpost', 'calendaradmin/handlers/addpost.php'));
addDataHandler(array(MODUL, 'getposts', 'calendaradmin/handlers/getposts.php'));
addDataHandler(array(MODUL, 'delpost', 'calendaradmin/handlers/delpost.php'));
