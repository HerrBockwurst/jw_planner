<?php
session_start();
define('index', true);

require_once 'config.php';
require_once 'libs/functions.php';
require_once 'oop/mysql.php';
require_once 'oop/contentAdmin.php';
require_once 'oop/lang.php';
require_once 'oop/user.php';

if(!checkURL(0, 'load') && !checkURL(0, 'datahandler')) buildHeader(); //Header wenn nicht per Ajax geladen
elseif(checkURL(0, 'datahandler')) $content->loadHandler(getURL(1), getURL(2)); //Datahandler
elseif(checkURL(0, 'load') && checkURL(2, false)) $content->displayContent(getURL(1)); //Page ohne Subpage
elseif(checkURL(0, 'load')) $content->displayContent(getURL(1),getURL(2)); //Page mit Subpage