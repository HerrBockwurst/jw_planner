<?php 
global $mysql, $user;

$mysql->where('calendar.cid', $_POST['cid']);
$mysql->join(array("calendar" => 'vsid', "versammlungen" => "vsid"));
$mysql->select("calendar", array('versammlungen.vsid' => "vsid"), 1);

if($mysql->countResult() != 1) exit;
if($mysql->fetchRow()->vsid != $user->vsid) exit;

?>
<div style="text-align:right; padding: 2px;"><img src="images/close.png" class="clickable" id="delCal"></div>
<input type="hidden" id="hidden_cid" value="<?php echo $_POST['cid']?>">
<div class="headline"><?php displayString('calendaradmin repeatingPosts') ?></div>
<div style="width: 708px; margin: 0 auto;">
	<table>
		<tr>
			<th><?php displayString('common monday')?></th>
			<th><?php displayString('common tuesday')?></th>
			<th><?php displayString('common wednesday')?></th>
			<th><?php displayString('common thursday')?></th>
			<th><?php displayString('common friday')?></th>
			<th><?php displayString('common saturday')?></th>
			<th><?php displayString('common sunday')?></th>
		</tr>
		<tr>
			<td>
				<input type="hidden" value="1" />
				<?php getPatternByDay(1, $_POST['cid']); ?>
				<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
			</td>
			<td>
				<input type="hidden" value="2" />
				<?php getPatternByDay(2, $_POST['cid']); ?>
				<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
			</td>
			<td>
				<input type="hidden" value="3" />
				<?php getPatternByDay(3, $_POST['cid']); ?>
				<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
			</td>
			<td>
				<input type="hidden" value="4" />
				<?php getPatternByDay(4, $_POST['cid']); ?>
				<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
			</td>
			<td>
				<input type="hidden" value="5" />
				<?php getPatternByDay(5, $_POST['cid']); ?>
				<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
			</td>
			<td>
				<input type="hidden" value="6" />
				<?php getPatternByDay(6, $_POST['cid']); ?>
				<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
			</td>
			<td>
				<input type="hidden" value="7" />
				<?php getPatternByDay(7, $_POST['cid']); ?>
				<div class="post clickable" data-id="addnew"><?php displayString('calendaradmin addpost')?></div>
			</td>
		</tr>
	</table>
	
	<div class="headline" style="margin-top: 10px"><?php displayString('calendaradmin generatePosts')?></div>
	<div id="generator">
		<label>
			<?php displayString('common from')?>
			<input type="text" id="genFrom" value="<?php echo date("d.m.Y", time())?>">
		</label>
		<label>
		<?php displayString('common to')?>
		<input type="text" id="genTo" value="<?php echo cal_days_in_month(CAL_GREGORIAN, intval(date("m", time())), intval(date("Y", time()))).".".date("m", time()).".".date("Y", time())?>">
		</label>
		<button id="b_genPosts"><?php displayString('calendaradmin generate')?></button>
		<div class="error" style="margin-top: 10px;"></div>
		<div class="warn" style="margin-top: 10px;"></div>
		<div class="success" style="margin-top: 10px;"></div>
	</div>	
</div>
<script>
$('#delCal').click(function() {
	if(confirm('<?php displayString('calendaradmin delete')?>')) {
		$.post('<?php echo PROTO.HOME?>/datahandler/calendaradmin/delcal', {cid: $('#hidden_cid').val()}, function(data) {
			if(testJSON(data)) {
				jdata = JSON.parse(data);
				alert(jdata.error);
				return;
			}
			$('#postmanager').stop().fadeOut(100).html('');
			loadContent('<?php echo PROTO.HOME?>/load/calendaradmin/calendars', '#calarea');
		});
	}
});
$(".post[data-id='addnew']").click(function(event) {
	setTimeout(function() { $('.tooltip').css({top: event.pageY, left: event.pageX}).fadeIn(100) }, 100);
	$('.tooltip').stop().fadeOut(100);
	$('#day').val($(event.target).siblings('input').val());
});
$('#b_genPosts').click(function() {
	$('.tooltip').stop().fadeOut(100);
	setTimeout(function() {
		if(confirm("<?php displayString('calendaradmin genPostsWarn')?>")) {
			var postdata = {};
			postdata.cid = $('#hidden_cid').val();
			postdata.start = $('#genFrom').val();
			postdata.end = $('#genTo').val();
			$.post('<?php echo PROTO.HOME?>/datahandler/calendaradmin/generateposts', postdata, function(data) {
				console.log(data);
				if(testJSON(data)) {
					jdata = JSON.parse(data);
					if(typeof jdata.error !== "undefined") $('#generator').find('.error').stop().fadeOut(0).html(jdata.error).delay(100).fadeIn(100).delay(3000).fadeOut(100);
					else if(typeof jdata.warn !== "undefined") $('#generator').find('.warn').stop().fadeOut(0).html(jdata.warn).delay(100).fadeIn(100).delay(3000).fadeOut(100);
					$('#generator').find('.success').stop().fadeOut(0).html(jdata.success).delay(100).fadeIn(100).delay(3000).fadeOut(100);
					return;
				}
				
			});
		}
	}, 150);
});
$(".post[data-id!='addnew']").click(function() {
	$('.tooltip').stop().fadeOut(100);
	var item = $(this);
	setTimeout(function() {
		if(confirm("<?php displayString('calendaradmin delPost')?>")) {
			$.post('<?php echo PROTO.HOME?>/datahandler/calendaradmin/delpost', {patt_id: item.attr('data-patt')}, function(data) {
				if(testJSON(data)) {
					jdata = JSON.parse(data);
					alert(jdata.error);
					return;
				}			
				loadContent('<?php echo PROTO.HOME?>/load/calendaradmin/getposts', '#postmanager', {cid: $('#hidden_cid').val()});
			});
		}
	},150);
});
</script>