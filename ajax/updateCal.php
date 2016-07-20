<?php
if(!isset($fromIndex)) exit;

require_once 'oop/calendar.php';

?>
<div>
	<div id="maincalendar">
		<?php $calendar->createMainCal($_POST['csel']); ?>
	</div>
	
	<div id="smallcalendar">
		<?php $calendar->createSmallCalTables($_POST['csel']); ?>
	</div>
</div>