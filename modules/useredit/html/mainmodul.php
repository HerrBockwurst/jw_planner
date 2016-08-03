<?php
if(!defined('index')) exit;
?>

<div id="useredit" class="modul" style="width: 500px; height: 500px;">
	<div class="modulheadline"><img src="images/close.png" onclick="closeModule('#useredit')" class="pointer" /></div>
	<div class="inner" id="useredit_inner">
		<?php $bob->startForm('usersearch'); ?>
		<fieldset>
			<legend><?php displayString('admin>usersearch');?></legend>
			<?php 
			$bob->addFormRow('username', getString('common>username'), array('text'));
			$bob->addFormRow('versammlung', getString('common>versammlung'), array('text'));
			$bob->addButton(getString('admin>search'));
			?>
		</fieldset>
		<?php $bob->endForm(); ?>
	</div>
</div>
<script>
	$('#usersearch').submit(function (event) {
		event.preventDefault();
		
		var username = $('#username').val(),
			versammlung = $('#versammlung').val();

		var posting = $.post('<?php displayHandlerURL('searchuser'); ?>', {'username': username, 'versammlung': versammlung});

		posting.done(function( data ) {
			data = data.replace(/\n/g, '');			

			$.post('<?php echo PROTO.HOME; ?>/ajax/load/modul/useredit/search', {'data' :data} , function(data) { $('#usersearch_window').remove(); $('#site').append(data); });
			/*$('#site').append()
			
			if(typeof newdata.error !== "undefined") {
				$('#loginerror').html('').html(newdata.error[0]).show(100).delay(2000).hide(100);
				return;
			}
			$('#login').fadeOut(800);				
			setTimeout(function() {
				$('#site').load(url + '/ajax/load', {page: 'default'});
			}, 800);	*/
		});
		
	});
</script>
<script class="removeme">$(openModule('#useredit'));</script>