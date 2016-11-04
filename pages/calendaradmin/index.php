<div id="editcal" class="fieldset">
	<div class="headline"><?php displayString('calendaradmin headline')?></div>
	<div id="calarea">
		<?php displayString('common loading')?>
	</div>
	<div id="postmanager" style="display: none"></div>
</div>
<div class="tooltip">
	<input type="hidden" id="day" value="" />
	<div id="start_time" style="text-align: center; float: left; margin-right: 30px;">
		<div style="font-weight: bold; font-size: 18px; margin-bottom: 5px;"><?php displayString('common start')?></div>
		<div style="float: left">
			<div class="timefield clickable" data-action="plus">+</div>
			<div class="timefield" data-action="hour"><input type="text" value="12" /></div>
			<div class="timefield clickable" data-action="minus">-</div>
		</div>
		<div style="float: left; height: 106px; line-height: 106px; font-size: 30px; width: 20px;">:</div>
		<div style="float: left">
			<div class="timefield clickable" data-action="plus">+</div>
			<div class="timefield" data-action="min"><input type="text" value="00" /></div>
			<div class="timefield clickable" data-action="minus">-</div>
		</div>
		<br class="floatbreak" />
	</div>
	<div id="end_time" style="text-align: center; float: left; margin-right: 30px;">
		<div style="font-weight: bold; font-size: 18px; margin-bottom: 5px;"><?php displayString('common end')?></div>
		<div style="float: left">
			<div class="timefield clickable" data-action="plus">+</div>
			<div class="timefield" data-action="hour"><input type="text" value="13" /></div>
			<div class="timefield clickable" data-action="minus">-</div>
		</div>
		<div style="float: left; height: 106px; line-height: 106px; font-size: 30px; width: 20px;">:</div>
		<div style="float: left">
			<div class="timefield clickable" data-action="plus">+</div>
			<div class="timefield" data-action="min"><input type="text" value="00" /></div>
			<div class="timefield clickable" data-action="minus">-</div>
		</div>
		<br class="floatbreak" />
	</div>
	<div id="counter" style="text-align: center; float: left;">
		<div style="font-weight: bold; font-size: 18px; margin-bottom: 5px;"><?php displayString('common count')?></div>
		<div style="margin: 0 auto; width: 52px;">
			<div class="timefield clickable" style="width: 50px;" data-action="plus">+</div>
			<div class="timefield" style="width: 50px;" data-action="count"><input style="width: 45px;" type="text" value="2" /></div>
			<div class="timefield clickable" style="width: 50px;" data-action="minus">-</div>
		</div>
	</div>
	<br class="floatbreak" />
	<div class="error" style="margin-top: 10px;"></div>
	<button id="b_addPost" style="margin-top: 20px;"><?php displayString('calendaradmin addPost')?></button>
</div>
<script>
/*
 * Skript für Tooltip
 */

$(".timefield[data-action='hour']").children('input').change(function() {

	var newval = parseInt($(this).val());
	
	if(!Number.isInteger(newval)) $(this).val(12);
	if(newval > 23) newval = 23;
	if(newval < 0) newval = 0;

	newval = "0" + newval;
	newval = newval.substring(newval.length - 2);

	$(this).val(newval);
});

$(".timefield[data-action='min']").children('input').change(function() {

	var newval = parseInt($(this).val());
	
	if(!Number.isInteger(newval)) $(this).val(00);
	if(newval > 59) newval = 59;
	if(newval < 0) newval = 0;

	if(newval >= 0 && newval < 8) newval = 0;
	else if(newval >= 8 && newval < 24) newval = 15;
	else if(newval >= 24 && newval < 38) newval = 30;
	else if(newval >= 38 && newval < 53) newval = 45;
	else newval = 0;

	newval = "0" + newval;
	newval = newval.substring(newval.length - 2);

	$(this).val(newval);
});
$(".timefield[data-action='count']").children('input').change(function() {

	var newval = parseInt($(this).val());
	
	if(!Number.isInteger(newval)) $(this).val(2);
	if(newval < 1) newval = 1;
	
	$(this).val(newval);
});

$(".timefield[data-action='plus'], .timefield[data-action='minus']").click(function() {
	var container = $(this).attr("data-action") == "plus" ? $(this).next() : $(this).prev();
	var input = container.children('input');
	var val = parseInt(input.val());

	if(container.attr("data-action") == "hour") {
		val = $(this).attr("data-action") == "plus" ? val + 1 : val - 1;
		input.val(val);
		input.change();
	} else if(container.attr("data-action") == "min") {
		val = $(this).attr("data-action") == "plus" ? val + 15 : val - 15;
		input.val(val);
		input.change();		
	} else if(container.attr("data-action") == "count") {
		val = $(this).attr("data-action") == "plus" ? val + 1 : val - 1;		
		input.val(val);
		input.change();
	}		
});

$("#b_addPost").click(function() {

	$('.tooltip').find('.error').stop().fadeOut(100);
	var postdata = {};
	
	postdata.start = $('#start_time').find(".timefield[data-action='hour']").children('input').val() + ":" + $('#start_time').find(".timefield[data-action='min']").children('input').val();
	postdata.end = $('#end_time').find(".timefield[data-action='hour']").children('input').val() + ":" + $('#end_time').find(".timefield[data-action='min']").children('input').val();
	postdata.count = parseInt($('#counter').find('input').val());
	postdata.cid = $('#hidden_cid').val();
	postdata.day = $('#day').val();
	
	$.post('<?php echo PROTO.HOME?>/datahandler/calendaradmin/addpattern', postdata, function(data) {
		if(testJSON(data)) {
			jdata = JSON.parse(data);			
			$('.tooltip').find('.error').stop().fadeOut(0).text(jdata.error).fadeIn(100).delay(3000).fadeOut(100);
			return;
		}
		
		$(".tooltip").fadeOut(100);
		console.log(data);
		loadContent('<?php echo PROTO.HOME?>/load/calendaradmin/getposts', '#postmanager', {cid: $('#hidden_cid').val()});
	});	
});

$(function() {
	//Erste if, damit er nur die Kalender läd, wenn das Modul neu geöffnet wurde
	if(!$('#hidden_cid').length) loadContent('<?php echo PROTO.HOME?>/load/calendaradmin/calendars', '#calarea');
	
});
</script>