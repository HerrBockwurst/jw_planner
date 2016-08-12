<?php
if(!defined('index')) exit;
global $user;
if(!$user->hasPerm('admin.useredit')) exit;
?>

<div id="useredit" class="modul" style="width: 550px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#useredit')" class="clickable" /></div>
	<div class="inner" id="useredit_inner">
		<?php $bob->startForm('usersearch'); ?>
		<fieldset id="usearch_fieldset">
			<legend><?php displayString('admin>usersearch');?></legend>
			<?php 
			$bob->addFormRow('username', getString('common>username'), array('text'));
			$bob->addFormRow('versammlung', getString('common>versammlung'), array('text'));
			$bob->addButton(getString('admin>search'));
			?>
		</fieldset>
		<?php $bob->endForm(); ?>
		
		
		<div class="clickable" onclick="loadModule('<?php echo PROTO.HOME ?>/ajax/load/modul/useredit/adduser', '#adduser_window')" id="adduser_button"><?php displayString('admin>add_user') ?></div>
	</div>	
</div>


<script>
	$('#usersearch').submit(function (event) {
		event.preventDefault();
		
		var username = $('#username').val(),
			versammlung = $('#versammlung').val();

		var posting = $.post('<?php displayHandlerURL('searchuser'); ?>', {'username': username, 'versammlung': versammlung});

		posting.done(function( data ) {	
			
			$.post('<?php echo PROTO.HOME; ?>/ajax/load/modul/useredit/search', {'data' :data} , function(data) {
				$('#usersearch_window').remove(); $('#site').append(data);
			});
		});
		
	});
</script>
<script class="removeme">$(openModule('#useredit'));</script>