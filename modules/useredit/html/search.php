<?php
if(!defined('index')) exit;
global $user;
if(!$user->hasPerm('admin.useredit')) exit;

$data = json_decode($_POST['data'], true);
?>

<div id="usersearch_window" class="modul" style="width: 450px; height: 200px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#usersearch_window')" class="clickable" /></div>
	<div class="inner" id="usersearch_inner">
		<div class="error smallmargin" id="usersearch_error"></div>
		<?php if(isset($data['error'])): ?>
			<div class="error smallmargin" style="display:block;"><?php echo $data['error'][0] ?></div>
		<?php else: ?>
		<div class="moremargin bold biggertext"><?php displayString('admin>usersearch_results')?>:</div>
		<div class="smallmargin">
			<?php
			while($cdata = current($data)):
				?>
				
				<div onclick="loadeditor('<?php echo $cdata['uid']; ?>')" class="clickable relative searchlist <?php if((key($data) % 2) == 0) echo "darker"?>">
					<span><?php echo $cdata['name']." (".$cdata['uid'].")"; ?></span>
					<span class="vsright"><?php echo $cdata['vname']?></span>
				</div>
				<?php 
				next($data);
			endwhile;
			?>
		</div>
		<?php endif; ?>				
	</div>
</div>

<script>
	function loadeditor(uid) {
		var posting = $.post('<?php echo PROTO.HOME?>/ajax/datahandler/edituser', {uid: uid});
		posting.done(function(data) {
			jdata = JSON.parse(data);
			if(typeof jdata.error !== "undefined") {
				$('#usersearch_error').text(jdata.error).show(100).delay(3000).hide(100);				
				return;
			}
			loadModule('<?php echo PROTO.HOME?>/ajax/load/modul/useredit/edituser', '#edituser_window', jdata);
		});
	}
</script>
<script class="removeme">$(openModule('#usersearch_window'));</script>