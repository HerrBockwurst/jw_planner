<?php
if(!isset($fromIndex)) exit;

require_once 'libs/calendar.php';

$result = $mysql->execute("SELECT * FROM `calendar` WHERE `cid` = ? AND `versammlung` = ? LIMIT 1", 'ss',
							array($_POST['cid'], $USER->vsid));
if($result->num_rows != 1):
	?><div class="error"><?php displayText('errors>noperm');?></div><?php
	exit;
endif;

/*
 * Anzuzeigenden Monat festlegen
 * Entweder aktueller(bei neuer Kalenderauswahl, sonst per Post übergebener Kalender
 */

if(!isset($_POST['month'])) $month = date("n");
else $month = $_POST['month'];

echo "geht";

/*
 * Monatsraster erstellen
 */

?>

