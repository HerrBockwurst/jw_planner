<?php
if(!isset($fromIndex)) exit;

require_once 'oop/calendar.php';

if(!isset($_POST['csel'])) $_POST['csel'] = null;
?>

<div class="field">
	<div class="headline"><?php displayText('common>calendar')?></div>
	<?php if(isset($nocal)): ?>
		<div class="morespace"><?php displayText('common>no_cal_applied')?></div>
	<?php endif;?>
	<div id="cal">	
		<div>
			<div id="maincalendar">
				<?php $calendar->createMainCal($_POST['csel']); ?>
			</div>
		
			<div id="smallcalendar">
				<?php $calendar->createSmallCalTables($_POST['csel']); ?>
			</div>
		</div>
	</div>
</div>
