<?php
if(!defined('index')) exit;

$data = array('groups', 'index.php', MENUVIS, 'admin.groups');
registerModul($data);
//addDataHandler(array(MODUL, 'query', 'sql/handlers/query.php'));