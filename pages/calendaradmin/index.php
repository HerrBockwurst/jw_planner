<div id="editcal" class="fieldset">
	<div class="headline"><?php displayString('calendaradmin headline')?></div>
	<div id="calarea">
		<?php displayString('common loading')?>
	</div>
	<div id="postmanager" style="display: none">
		
	</div>
</div>
<div class="tooltip">
	<div id="start_time" style="text-align: center; float: left; margin-right: 30px;">
		<div style="font-weight: bold; font-size: 18px; margin-bottom: 5px;"><?php displayString('common start')?></div>
		<div style="float: left">
			<div class="timefield clickable" data-action="plus">+</div>
			<div class="timefield" data-action="hour">12</div>
			<div class="timefield clickable" data-action="minus">-</div>
		</div>
		<div style="float: left; height: 106px; line-height: 106px; font-size: 30px; width: 20px;">:</div>
		<div style="float: left">
			<div class="timefield clickable" data-action="plus">+</div>
			<div class="timefield" data-action="min">00</div>
			<div class="timefield clickable" data-action="minus">-</div>
		</div>
		<br class="floatbreak" />
	</div>
	<div id="end_time" style="text-align: center; float: left; margin-right: 30px;">
		<div style="font-weight: bold; font-size: 18px; margin-bottom: 5px;"><?php displayString('common end')?></div>
		<div style="float: left">
			<div class="timefield clickable" data-action="plus">+</div>
			<div class="timefield" data-action="hour">13</div>
			<div class="timefield clickable" data-action="minus">-</div>
		</div>
		<div style="float: left; height: 106px; line-height: 106px; font-size: 30px; width: 20px;">:</div>
		<div style="float: left">
			<div class="timefield clickable" data-action="plus">+</div>
			<div class="timefield" data-action="min">00</div>
			<div class="timefield clickable" data-action="minus">-</div>
		</div>
		<br class="floatbreak" />
	</div>
	<br class="floatbreak" />
</div>
<script>
$(function() {
	//Erste if, damit er nur die Kalender läd, wenn das Modul neu geöffnet wurde
	if(!$('#hidden_cid').length) loadContent('<?php echo PROTO.HOME?>/load/calendaradmin/calendars', '#calarea');
	
});
</script>