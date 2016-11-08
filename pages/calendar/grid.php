<?php
if(!isset($_POST)) returnErrorJSON('No Data given!');

global $mysql, $user;

$mysql->where('calendar.cid', $_POST['cid']);
$mysql->join(array("calendar" => 'vsid', "versammlungen" => "vsid"));
$mysql->select("calendar", array('versammlungen.vsid' => "vsid"), 1);

if($mysql->countResult() != 1) exit;
if($mysql->fetchRow()->vsid != $user->vsid) exit;


$daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $_POST['month'], $_POST['year']);
$month = intval($_POST['month']);
$year = intval($_POST['year']);
$pointerCol = 1;
$currDay = 1;

$startStamp = strtotime("1.".$month.".".$year);
$endStamp = strtotime($daysOfMonth.".".$month.".".$year) + (60*60*24) - 1;

/*
 * Termine auslesen
 */

$daysWithPosts = array();

$mysql->where('start', $startStamp, '>=');
$mysql->where('end', $endStamp, '<=');
$mysql->where('cid', $_POST['cid']);
$mysql->select('posts', array('start'));

$result = $mysql->fetchAll();
foreach($result AS $currPost) $daysWithPosts[date('j', $currPost['start'])] = 1;

/*
 * Tabelle schreiben
 */

echo "<table id=\"c_table\">";
echo "<tr>
		<th class=\"shader\">".substr(getString('common monday'),0, 2)."</th>
		<th class=\"shader\">".substr(getString('common tuesday'),0, 2)."</th>
		<th class=\"shader\">".substr(getString('common wednesday'),0, 2)."</th>
		<th class=\"shader\">".substr(getString('common thursday'),0, 2)."</th>
		<th class=\"shader\">".substr(getString('common friday'),0, 2)."</th>
		<th class=\"shader\">".substr(getString('common saturday'),0, 2)."</th>
		<th class=\"shader\">".substr(getString('common sunday'),0, 2)."</th>
</tr>";

while($pointerCol < 8) {
	while($currDay <= $daysOfMonth) {
		if($pointerCol == 1) echo "<tr>";
		
		$weekdayOfcurrDay = date("N", strtotime($currDay.".".$month.".".$year)) == 0 ? 7 : date("N", strtotime($currDay.".".$month.".".$year));		
		
		if($weekdayOfcurrDay == $pointerCol) {
			$highlight = isset($daysWithPosts[$currDay]) ? "clickable highlight" : ""; 
			echo "<td class=\"$highlight\" data-date=\"$currDay\">".$currDay."</td>";
			$currDay++;
		}
		else echo "<td class=\"shader\"></td>";
		
		$pointerCol++;
		if($pointerCol > 7) {
			echo "</tr>";
			$pointerCol = 1;
		}
	}
	
	echo "<td class=\"shader\"></td>";
	$pointerCol++;
	if($pointerCol > 7) {
		echo "</tr>";
	}
}

echo "</table>";
?>
<div id="c_backbutton" class="clickable">&#10096;</div>
<div id="c_postentry"><?php displayString('common loading')?></div>

<script>
var heightChange = false;

$('td.clickable').click(function() {

	var attrA = {height: ($('#c_table').height() - 22) +  'px', "line-height": ($('#c_table').height()) +  'px'}
	var postdata = {};
	var calswitch = $('#switch_main');
	
	postdata.date = $(this).attr('data-date') + "." + calswitch.attr('data-currmonth') + "." + calswitch.attr('data-curryear');
	postdata.cid = $('#c_calheader').find("div[data-active='1']").attr('data-cid');

	$.post('<?php echo PROTO.HOME?>/datahandler/calendar/getposts', postdata, function(data) {
		$('#c_postentry').html(data);

		heightChange = false;
		if($('#c_cal').height() < $('#daycontainer').height()) {
			$('#c_cal').animate({height: $('#daycontainer').height() + "px"}, 100);
			attrA = {height: ($('#daycontainer').height() - 22) +  'px', "line-height": ($('#daycontainer').height()) +  'px'}
			heightChange = true;
		}
		
		$('#c_postentry').animate({left: "70px"}, 500);
		$('#c_backbutton').css(attrA).animate({left: "10px"}, 500); 
		$('#c_table').animate({left: "-" + ($("#c_table").width() + $('#c_table').offset().left) + "px"}, 500);	
	});
});

$('#c_backbutton').click(function() {

	if(heightChange) $('#c_cal').delay(500).animate({height: $('#c_table').height() + "px"}, 100);
		
	$('#c_backbutton').animate({left: "-50px"}, 500);
	$('#c_table').animate({left: "0px"}, 500);
	$('#c_postentry').animate({left: $('#c_cal').width() + "px" });
});
</script>