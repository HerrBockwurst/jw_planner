<?php
global $mysql, $user;

$mysql->where('calendar.cid', $_POST['cid']);
$mysql->join(array("calendar" => 'vsid', "versammlungen" => "vsid"));
$mysql->select("calendar", array('versammlungen.vsid' => "vsid"), 1);

if($mysql->countResult() != 1) exit;
if($mysql->fetchRow()->vsid != $user->vsid) exit;

?>

<div id="monthswitch">
	<div id="switch_pre" class="clickable">&#10096;</div>
	<div id="switch_next" class="clickable">&#10097;</div>
	<div id="switch_main" data-currMonth="<?php echo date('m', time())?>" data-currYear="<?php echo date('Y', time())?>">November 2016</div>
</div><br class="floatbreak" />
<div id="c_cal">
	<?php displayString('common loading')?>
</div>

<script>
$(function() {
	loadContent('<?php echo PROTO.HOME?>/load/calendar/getgrid', '#c_cal');
});
</script>