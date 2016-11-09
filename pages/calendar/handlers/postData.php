<?php
if(!isset($_POST['pid'])) exit;

global $user, $mysql;

$mysql->where('pid', $_POST['pid']);
$mysql->join(array('posts' => 'cid', 'calendar' => 'cid'));
$mysql->select('posts', array('*', 'calendar.vsid' => 'vsid'), 1);

$postdata = $mysql->fetchRow();
$entrys = json_decode($postdata->entrys);

if($postdata->vsid != $user->vsid) {
	displayString('errors noPerm');
	exit;
}

?>
<div id="c_p_userholder">
	<strong><?php echo preg_replace('/\{1\}/', $postdata->count, getString('calendar entrys'));?></strong> 
	<?php
	if(count($entrys) == 0) echo "<div class=\"c_p_userrow\">".getString('calendar noEntrys')."</div>";
	
	foreach($entrys AS $entry):
		$mysql->where('uid', $entry);
		$mysql->select('users', array('name'));
		$name = explode(" ", ($mysql->fetchRow()->name));
	?>
	
	<div class="c_p_userrow" data-uid="<?php echo $entry?>">
		<div style="background-color: rgba(<?php echo stringToColorCode(implode(" ", $name))?>)"><?php echo strtoupper(substr($name[0], 0, 1)).strtoupper(substr($name[count($name) - 1], 0, 1)); ?></div>
		<?php echo implode(" ", $name)?>
		<?php
			if($user->hasPerm('calender.entry.other')) {
				echo "<div class=\"clickable deletebutton\">".getString('calendar delete')."</div>";
			}
		?>
	</div>
	
	<?php endforeach; ?>	
</div>
<button id="b_sign" <?php echo in_array($user->uid, $entrys) ? "style=\"border: 1px solid rgba(128,0,0,0.5); background-color: rgba(128,0,0,0.5);\"" : "";?>>
	<?php echo in_array($user->uid, $entrys) ? getString('calendar signOut') : getString('calendar signIn')?>
</button>
<?php if($user->hasPerm('admin.calendar')):?>
<button id="b_delPost" style="border: 1px solid rgba(128,0,0,0.5); background-color: rgba(128,0,0,0.5);">
	<?php displayString('calendar deletePost')?>
</button>
<?php endif;?>
<script>
var pid = <?php echo $_POST['pid']?>;
$('.deletebutton').click(function() {
	$.post('<?php echo PROTO.HOME?>/datahandler/calendar/deluser', {pid: pid, uid: $(this).parent().attr('data-uid')}, function(data) {
		if(testRedirect(data)) return;		
		if(testJSON(data)) {
			jdata = JSON.parse(data);
			alert(jdata.error);
			return;
		}

		$.post('<?php echo PROTO.HOME?>/datahandler/calendar/getposts', updateData, function(data) {
			if(testRedirect(data)) return;

			$('#postdata').stop().hide("slide", {direction: "left"}, 100);

			setTimeout(function() {
				$.post('<?php echo PROTO.HOME?>/datahandler/calendar/getpostdata', {pid: pid}, function(data2) {
					if(testRedirect(data2)) return;
					
					$('#c_postentry').html(data);
					$('#postdata').html(data2).show("slide", {direction: "left"}, 100);
				});				
			},150);
		});
	});
});

$('#b_sign').click(function() {
	$.post('<?php echo PROTO.HOME?>/datahandler/calendar/entry', {pid: pid}, function(data) {
		if(testRedirect(data)) return;		
		if(testJSON(data)) {
			jdata = JSON.parse(data);
			alert(jdata.error);
			return;
		}

		$.post('<?php echo PROTO.HOME?>/datahandler/calendar/getposts', updateData, function(data) {
			if(testRedirect(data)) return;

			$('#postdata').stop().hide("slide", {direction: "left"}, 100);

			setTimeout(function() {
				$.post('<?php echo PROTO.HOME?>/datahandler/calendar/getpostdata', {pid: pid}, function(data2) {
					if(testRedirect(data2)) return;
					
					$('#c_postentry').html(data);
					$('#postdata').html(data2).show("slide", {direction: "left"}, 100);
				});				
			},150);
		});
	});
});

$('#b_delPost').click(function() {

	if(!confirm('<?php displayString('calendar confPostDelete')?>')) return; 
	
	$.post('<?php echo PROTO.HOME?>/datahandler/calendar/delpost', {pid: pid}, function(data) {
		if(testRedirect(data)) return;	
			
		if(testJSON(data)) {
			jdata = JSON.parse(data);
			alert(jdata.error);
			return;
		}

		$.post('<?php echo PROTO.HOME?>/datahandler/calendar/getposts', updateData, function(data) {
			if(testRedirect(data)) return;

			$('#postdata').stop().hide("slide", {direction: "left"}, 100);
			$('#c_postentry').html(data);
		});
	});
});
</script>