<div id="cal" class="fieldset">
	<div class="headline"><?php displayString('menu calendar')?></div>
	<div id="c_calheader">
		<?php displayString('common loading')?>
	</div>
	<div id="c_calarea">
	</div>
</div>
<script>
$(function() {
	//Erste if, damit er nur die Kalender l�d, wenn das Modul neu ge�ffnet wurde
	if(!$('#hidden_cid').length) loadContent('<?php echo PROTO.HOME?>/load/calendar/getcals', '#c_calheader');
	
});
</script>