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

?>
<div id="c_backbutton" class="clickable">&#10096;</div>
<?php 


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
			$highlight = isset($daysWithPosts[$currDay]) ? "highlight" : ""; 
			echo "<td class=\"clickable $highlight\" data-date=\"$currDay\">".$currDay."</td>";
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
<br class="floatbreak" />
<script>
$('td.clickable').click(function() {

	var attr = {height: ($('#c_table').height() - 22) +  'px', "line-height": ($('#c_table').height()) +  'px'}
	
	$('#c_backbutton').css(attr).show('slide', {direction: 'left'}, 1000); 
	$('#c_table').hide('slide', {direction: 'left'}, 1000);
});
</script>