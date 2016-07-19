<?php
if(!isset($fromIndex)) exit;

$result = $mysql->execute("SELECT * FROM `calendar` WHERE `cid` = ? AND `versammlung` = ? LIMIT 1", 'ss',
							array($_POST['cid'], $USER->vsid));
if($result->num_rows != 1):
	?><div class="error smallspace"><?php displayText('errors>noperm');?></div><?php
	exit;
endif;

require_once 'oop/calendar.php';

?>
<div>
	<div id="maincalendar">
		<?php $calendar->createMainCal($_POST['csel'], $_POST['cid']); ?>
	</div>
	
	<div id="smallcalendar">
		<?php $calendar->createSmallCalTables($_POST['csel']); ?>
	</div>
</div>