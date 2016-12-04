<?php
if(!isset($_POST)) exit;

$starttime = strtotime($_POST['date']);

$endtime = $starttime + ((60*60*24) - 1);

global $mysql, $user;

$mysql->where('cid', $_POST['cid']);
$mysql->select('calendar', array('vsid'), 1);

if($mysql->fetchRow()->vsid != $user->vsid) displayString('errors noPerm');

$mysql->where('start', $starttime, '>=');
$mysql->where('end', $endtime, '<=');
$mysql->where('cid', $_POST['cid']);
$mysql->orderBy('start');
$mysql->select('posts');

?>
<div id="daycontainer">
	<div><?php displayString('common '.strtolower(date('l', $starttime))) ?>, <?php echo date("d.m.Y", $starttime)?></div>
	<div id="c_d_postcontainer">
		<div id="postcontainer_left">
<?php 
foreach($mysql->fetchAll() AS $currPost) {
	$shader = count(json_decode($currPost['entrys'])) >= intval($currPost['count']) ? "\" style=\"background-color: rgba(200,200,200,0.5); border-color: rgba(0,0,0,0.4)" : "";
	$shader = count(json_decode($currPost['entrys'])) > 0 && count(json_decode($currPost['entrys'])) < intval($currPost['count']) ? "\" style=\"background-image: background-image: linear-gradient(-15deg, rgba(0,179,9,0.5) 40%, rgba(0,179,9,0.0) 60%);" : "";
	
	echo "<div data-pid=\"".$currPost['pid']."\" class=\"clickable $shader\">".date('H:i', $currPost['start'])." - ".date('H:i', $currPost['end'])."</div>";
}
?>
		</div>
		<div id="postcontainer_right">
			<div id="postdata"></div>
		</div>
		<br class="floatbreak" />
	</div>
</div>
<script>
/*
 * Globale Variable um die Container auch von selbst heraus, ohne klick aktualisieren zu können
 */
var updateData = {date: '<?php echo $_POST['date']?>', cid: <?php echo $_POST['cid']?>};

$('#postcontainer_left').children('div').click(function() {
	$.post('<?php echo PROTO.HOME?>/datahandler/calendar/getpostdata', {pid: $(this).attr('data-pid')}, function(data) {
		if(testRedirect(data)) return;
		
		$('#postdata').stop().hide("slide", {direction: "left"}, 100);
		setTimeout(function() {
			$('#postdata').html(data).show("slide", {direction: "left"}, 100);
		}, 150);
	});
});

$(function() {
	$('#postcontainer_right').css({height: $('#postcontainer_left').height() + "px"});
});
</script>