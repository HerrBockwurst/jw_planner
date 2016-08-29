<?php
if(!defined('index')) exit;

$data = array('calendar', 'index.php', MENUVIS, 'calendar.entry');
registerModul($data);
addDataHandler(array(MODUL, 'loadposts', 'calendar/handlers/loadposts.php'));