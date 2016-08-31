<?php
if(!defined('index')) exit;

$data = array('sql', 'index.php', MENUVIS, 'system.query');
registerModul($data);
addDataHandler(array(MODUL, 'query', 'sql/handlers/query.php'));