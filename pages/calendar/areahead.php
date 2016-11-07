<?php
global $mysql, $user;

$mysql->where('calendar.cid', $_POST['cid']);
$mysql->join(array("calendar" => 'vsid', "versammlungen" => "vsid"));
$mysql->select("calendar", array('versammlungen.vsid' => "vsid"), 1);

if($mysql->countResult() != 1) exit;
if($mysql->fetchRow()->vsid != $user->vsid) exit;

?>

<div id="monthswitch" style="margin-top: 10px;">
	<div id="switch_pre" class="clickable">&#10096;</div>
	<div id="switch_next" class="clickable">&#10097;</div>
	<div id="switch_main" data-currmonth="<?php echo date('m', time())?>" data-curryear="<?php echo date('Y', time())?>">&nbsp;</div>
</div><br class="floatbreak" />
<div id="c_cal">
	<?php displayString('common loading')?>
</div>

<script>
function loadCal() {

	var months = ["", <?php 
		for($i = 1; $i <= 12; $i++) {
			echo "\"".getString('months '.strtolower(date('M', strtotime('1.'.$i.'.2000'))))."\"";
			if($i != 12) echo ", ";
		}		
	?>];
	
	$('#switch_main').text(months[$('#switch_main').attr('data-currmonth')] + " " + $('#switch_main').attr('data-curryear'));
	
	
	loadContent('<?php echo PROTO.HOME?>/load/calendar/getgrid', '#c_cal',
			{cid: <?php echo $_POST['cid']?>, month: $('#switch_main').attr('data-currmonth'), year:  $('#switch_main').attr('data-curryear')});	
}

$(loadCal);

$('#monthswitch').children('div').click(function() {
	if(!$(this).hasClass('clickable')) return;
	
	var mainswitch = $('#switch_main');
	var month = parseInt(mainswitch.attr('data-currmonth'));
	var year = parseInt(mainswitch.attr('data-curryear'));

	month = $(this).attr('id') == "switch_pre" ? month - 1 : month + 1;

	if(month < 1) {
		month = 12;
		year = year - 1;
	} else if(month > 12) {
		month = 1;
		year = year + 1;
	}

	mainswitch.attr('data-currmonth', month);
	mainswitch.attr('data-curryear', year);

	loadCal();
});
</script>