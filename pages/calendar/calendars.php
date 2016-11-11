<?php
global $mysql, $user;

$mysql->where('vsid', $user->vsid);
$mysql->select('calendar');

if($mysql->countResult() == 0) {
	displayString('calendar noCalsApplied');
	exit;
}

$calendars = $mysql->fetchAll();

foreach($calendars AS $currCal) {
	echo "<div data-cid=\"".$currCal['cid']."\" data-active=\"0\" class=\"switch_cal clickable\">".$currCal['name']."</div>";
}
?>

<script>
$(".switch_cal[data-active='0']").click(function() {
	$('.switch_cal').attr('data-active', 0);
	$(this).attr('data-active', 1);
	
	loadContent('<?php echo PROTO.HOME?>/load/calendar/getcal', '#c_calarea', {cid: $(this).attr('data-cid')});
})
</script>