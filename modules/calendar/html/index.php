<?php
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('calendar.entry')) exit;

/*
 * Kalender laden
 */
$cals = $mysql->execute("SELECT cid, name FROM calendar WHERE vsid = ?", 's', $user->vsid);
$cals = $cals->fetch_all(MYSQLI_ASSOC);
?>

<div id="calendar" class="modul" style="width: 600px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#calendar')" class="clickable" /></div>
	<div class="inner" id="calendaradmin_inner">
		<fieldset style="margin: 0px auto; width: 90%;">
			<legend><?php displayString('common>calendar'); ?></legend>
			<select id="cal_posts_selector">			
				<?php 
				
					if(empty($cals)):
						?><option value="0"><?php displayString('calendar>no_cal_applied')?></option><?php
					else:
						foreach($cals AS $cal):
							?><option value="<?php echo $cal['cid']?>"><?php echo $cal['name']?> (#<?php echo $cal['cid']?>)</option><?php 
						endforeach;
					endif;
				?>
			</select>
		</fieldset>
		<div class="error smallmargin" id="cal_entry_error"></div>
		<div class="success smallmargin" id="cal_entry_success"></div>
		<fieldset>
			<div id="calendar_posts_field">
			</div>
		</fieldset>
	</div>
	<script>
		function showtooltip(id) {
			$('#tooltip_' + id).show(100);
		}
		function hidetooltip(id) {
			$('#tooltip_' + id).hide(100);
		}
		function loadPosts(cid) {
			$.post('<?php echo PROTO.HOME?>/ajax/datahandler/loadposts', {cid: cid}, function(data){
				$('#calendar_posts_field').hide(100);
				setTimeout(function() {
					$('#calendar_posts_field').html(data).show(100);	
				}, 100);
			});
		}

		$('#cal_posts_selector').change(function() {
			loadPosts($('#cal_posts_selector').val());
		});

		function applyme(pid, cid) {
			$('#cal_entry_error').hide(100);
			$('#cal_entry_success').hide(100);

			$.post('<?php echo PROTO.HOME?>/ajax/datahandler/applyentry', {pid: pid, cid: cid}, function(data) {
				console.log(data);
				var jdata = JSON.parse(data);

				if(typeof jdata.error !== "undefined") {
					$('#cal_entry_error').text(jdata.error).show(100).delay(1000).hide(100);
					return;
				}

				$('#counter_' + pid).text(jdata.newcount);
				$('#tooltip_' + pid).html(jdata.tooltip);
				if($('#star_' + pid).css('display') != 'none') {
					$('#star_' + pid).hide(100);
				} else {
					$('#star_' + pid).show(100);
				}
				$('#cal_entry_success').text(jdata.success).show(100).delay(1000).hide(100);
			});
		}

		function deleteentry(eid) {
			$('#cal_entry_error').hide(100);
			$('#cal_entry_success').hide(100);

			$.post('<?php echo PROTO.HOME?>/ajax/datahandler/deleteentry', {eid: eid}, function(data) {
				var jdata = JSON.parse(data);

				if(typeof jdata.error !== "undefined") {
					$('#cal_entry_error').text(jdata.error).show(100).delay(1000).hide(100);
					return;
				}

				$('#counter_' + jdata.pid).text(jdata.newcount);
				$('#tooltip_' + jdata.pid).html(jdata.tooltip);
				if($('#star_' + jdata.pid).css('display') != 'none') {
					$('#star_' + jdata.pid).hide(100);
				} else {
					$('#star_' + jdata.pid).show(100);
				}
				$('#cal_entry_success').text(jdata.success).show(100).delay(1000).hide(100);
			});
		}
	</script>	
</div>
<script class="removeme">
	$(loadPosts($('#cal_posts_selector').val()));
</script>
<script class="removeme">$(openModule('#calendar'));</script>