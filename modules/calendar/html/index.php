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
		<fieldset>
			<div id="calendar_posts_field">
			</div>
		</fieldset>
	</div>
	<script>
		function showtooltip(id) {
			$('#' + id).show(100);
		}
		function hidetooltip(id) {
			$('#' + id).hide(100);
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
	</script>	
</div>
<script class="removeme">
	$(loadPosts($('#cal_posts_selector').val()));
</script>
<script class="removeme">$(openModule('#calendar'));</script>