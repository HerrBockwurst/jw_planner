<div id="cal" class="fieldset" style="max-width: 700px;">
	<div class="headline"><?php displayString('menu calendar')?></div>
	<div id="c_calheader">
		<?php displayString('common loading')?>
	</div>
	<div id="c_calarea" style="display:none; min-width: 600px">
	</div>
</div>
<script>
$(function() {
	//Erste if, damit er nur die Kalender läd, wenn das Modul neu geöffnet wurde
	if(!$('#c_calheader').children("div[data-active='1']").length) loadContent('<?php echo PROTO.HOME?>/load/calendar/getcals', '#c_calheader');
	
});
</script>