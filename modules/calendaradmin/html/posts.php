<?php 
if(!defined('index')) exit;
global $user, $mysql;
if(!$user->hasPerm('admin.calendar')) exit;

$cals = $mysql->execute("SELECT cid, name FROM calendar WHERE vsid = ?", 's', $user->vsid);
$cals = $cals->fetch_all(MYSQLI_ASSOC);

?>
<div id="cadmin_posts" class="modul" style="width: 700px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#cadmin_posts')" class="clickable" /></div>
	<div class="inner relative">
		<div id="c_posts_error" class="error smallmargin"></div>
		<div id="c_posts_success" class="success smallmargin"></div>
		<div style="margin: 10px auto; width: 90%;">
			<select id="c_posts_selector">
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
		</div>
		<fieldset id="cadmin_posts_field">
			<legend><?php displayString('admin>posts')?></legend>		
			<div id="cadmin_posts_field_content" style="display:none;">
			</div>	
		</fieldset>
		<fieldset id="cadmin_posts_add">
			<legend><?php displayString('admin>c_posts_add')?></legend>
			<?php 
			$bob->startForm('c_posts_add');
			$bob->addFormRow('c_posts_start', getString('common>start'), array('text'), date("d.m.Y H:00", time()));
			$bob->addFormRow('c_posts_end', getString('common>end'), array('text'), date("d.m.Y H:00", time() + 60*60));
			$bob->addFormRow('c_posts_expire', getString('admin>expire'), array('text'), date("d.m.Y H:00", time() + 60*60*24*31));
			$bob->addFormRow('c_posts_pers', getString('admin>appl_count'), array('text'), '2');
			$bob->addFormRow('c_posts_cid', '', array('hidden'));
			$bob->addButton(getString('admin>add'));
			$bob->endForm();
			?>
		
		</fieldset>
		<br class="floatbreak" /><br />
	</div>
	<script>
	function updatePosts(data) {
		setTimeout(function() {
			$('#cadmin_posts_field_content').html(data);
			}, 100);
		$('#cadmin_posts_field_content').hide(100).show(100);
	}
	
	$('#c_posts_selector').change(function() {
		$('#c_posts_cid').val($('#c_posts_selector').val());
		$.post('<?php echo PROTO.HOME?>/ajax/datahandler/getposts', {cid: $('#c_posts_selector').val()}, function(data) {
			updatePosts(data);
		});
	});
	
	var patt = new RegExp("[0-9]{1,2}.[0-9]{1,2}.[0-9]{4} [0-9]{1,2}:[0-9]{2}");
	$('#c_posts_start').change(function() {
		var string = $('#c_posts_start').val();
		if(patt.test(string)) {			
			var date = string.split('.');
			var time = date[2].split(' ');			
			
			var month = parseInt(date[1]),
				year = parseInt(date[2]);
			time = time[1];
			month = month + 1;
			if(month > 12) {
				year = year + 1;
				month = "01";
			} else if(month < 10) {
				month = "0" + month;
			}
			var newstring = date[0] + "." + month + "." + year + " " + time;
			$('#c_posts_expire').val(newstring);
		}
	});
	
	$('#c_posts_add').submit(function (event) {
		
		event.preventDefault();
	
		$('#c_posts_error').hide(100);
		$('#c_posts_success').hide(100);
		
		var c_posts_start = $('#c_posts_start').val(),
			c_posts_end = $('#c_posts_end').val(),
			c_posts_expire = $('#c_posts_expire').val(),
			c_posts_pers = $('#c_posts_pers').val(),
			c_posts_cid = $('#c_posts_cid').val();

		if(!patt.test(c_posts_start) || !patt.test(c_posts_end) || !patt.test(c_posts_expire)) {
			$('#c_posts_error').text('<?php displayString('errors>invalidFormSubmit')?>').show(100);
			return;
		}
	
		var posting = $.post('<?php echo PROTO.HOME?>/ajax/datahandler/addpost', {start: c_posts_start, end: c_posts_end, expire: c_posts_expire, pers: c_posts_pers, cid: c_posts_cid});
		posting.done(function(data) {
			var jdata = JSON.parse(data);
			console.log(jdata);
			if(typeof jdata.error !== "undefined") {
				if(typeof jdata.error == "object") {
					$('#c_posts_error').text(jdata.error[0]).show(100);
					return;
				} else {
					$('#c_posts_error').text(jdata.error).show(100);
					return;
				}
			}

			$('#c_posts_success').text(jdata.success[0]).show(100);
			$.post('<?php echo PROTO.HOME?>/ajax/datahandler/getposts', {cid: c_posts_cid}, function(data) {
				updatePosts(data);
			});
			
		});	
	});

	function deletepost(pid) {		
		$.post('<?php echo PROTO.HOME?>/ajax/datahandler/delpost', {pid: pid}, function(data) {
			jdata = JSON.parse(data);
			if(typeof jdata.error !== "undefined") {
				$('#c_posts_error').text(jdata.error[0]).show(100);
				return;
			}			
			$.post('<?php echo PROTO.HOME?>/ajax/datahandler/getposts', {cid: $('#c_posts_selector').val()}, function(data) {
				updatePosts(data);
			});
		});
	}
	</script>
</div>
<script class="removeme">
	$(function() {
		$('#c_posts_cid').val($('#c_posts_selector').val());
		$.post('<?php echo PROTO.HOME?>/ajax/datahandler/getposts', {cid: $('#c_posts_cid').val()}, function(data) {
			$('#cadmin_posts_field_content').html(data).show(100);
		});
	});
</script>
<script class="removeme">$(openModule('#cadmin_posts'));</script>