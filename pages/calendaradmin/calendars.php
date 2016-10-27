<?php
global $mysql, $user;

$mysql->where('vsid', $user->vsid);
$mysql->select('calendar');
$calendars = $mysql->fetchAll();

foreach($calendars AS $currCal) {
	echo "<div data-cid=\"".$currCal['cid']."\" data-active=\"0\" class=\"switch_cal clickable\">".$currCal['name']."</div>";
}
?>
<div class="switch_cal">
	<input type="text" id="input_addcal" />
	<span id="button_addcal" class="clickable">+</span>
</div>
<script>
$(".switch_cal[data-active='0']").click(function() {
	$('.switch_cal').attr('data-active', 0);
	$(this).attr('data-active', 1);
	loadContent('<?php echo PROTO.HOME?>/load/calendaradmin/getposts', '#postmanager', {cid: $(this).attr('data-cid')});
})

$('#button_addcal').click(function() {
	$.post('<?php echo PROTO.HOME?>/datahandler/calendaradmin/addcal', {name: $('#input_addcal').val()}, function(data) {
		if(testJSON(data)) {
			jdata = JSON.parse(data);
			alert(jdata.error);
			return;
		}
		$('#postmanager').fadeOut(0);
		loadContent('<?php echo PROTO.HOME?>/load/calendaradmin/calendars', '#calarea');		
	});
});
</script>